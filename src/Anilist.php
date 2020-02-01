<?php

namespace CheckerAnilist;

use CheckerAnilist\Client\AnilistClient;
use CheckerAnilist\Model\Anime;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Anilist
{

    private $parsingQueue = [];
    private $unreferencedQueue = [];
    private $hiyoriDb = [];

    private $page;
    private $lastPage;


    // Dump Data to mongodb after self::autosave entries are added to $parsingQueue
    public  $autosave = 500;


    private $dotenv;
    private $anilist;
    private $guzzle;
    private $mongodb;
    private $query;
    private $log;

    /**
     * @var Anime[]
     */
    private $database;

    /**
     * @var ResponseInterface
     */
    private $response;
    /**
     * @var Object
     */
    private $responseBody;
    /**
     * @var int
     */
    private $requestStatusCode;
    /**
     * @var int
     *
     * Number of accepted requests remaining in a minute (90 requests/min)
     */
    private $rateLimit;
    /**
     * @var int
     *
     * Number of accepted requests remaining in a minute (max 90 requests/min)
     */
    private $rateLimitRemaining;
    /**
     * @var int
     *
     * Unix timestamp of when we can make another accepted request
     */
    private $rateLimitReset;
    /**
     * @var int
     *
     * Seconds we need to wait until we can make another accepted request
     */
    private $rateLimitRetryAfter;
    private $sleeping = false;

    public function __construct()
    {
        $this->dotenv = new Dotenv();
        $this->dotenv->load(__DIR__.'/../.env');

        $this->log = new Logger('checker');
        $this->log->pushHandler(new StreamHandler(__DIR__.'/event.log', Logger::DEBUG));

        $this->guzzle = new Client([
            'http_errors' => false
        ]);
        $this->anilist = new AnilistClient($this->guzzle);
        $this->mongodb = new \MongoDB\Client(
            sprintf(
                'mongodb+srv://%s:%s@%s',
                $_ENV['MONGODB_USERNAME'],
                $_ENV['MONGODB_PASSWORD'],
                $_ENV['MONGODB_CONNECTION_STRING']
            )
        );

        $this->page = 1;
        $this->lastPage = 1;


        $this->query = file_get_contents(
            __DIR__.'/Data/request.graphql'
        );
    }

    public function start()
    {
        while ($this->page <= $this->lastPage) {

            if ($this->sleeping) {
                $this->log->debug("Waking up...");
                echo "Waking up...\n";
                $this->sleeping = false;
            }

            $this->log->debug("Fetching page {$this->page}/{$this->lastPage}");
            echo "Fetching page {$this->page}/{$this->lastPage}\n";

            $this->response = $this->anilist->request(
                $this->query,
                [
                    'page' => $this->page
                ]
            );
            $this->requestStatusCode = $this->response->getStatusCode();
            $this->rateLimit = (int) $this->response->getHeader('X-RateLimit-Limit')[0];
            $this->rateLimitRemaining = (int) $this->response->getHeader('X-RateLimit-Remaining')[0];

            $this->log->debug("Response [{$this->requestStatusCode}]; Requests remaining [{$this->rateLimitRemaining}/{$this->rateLimit}]");
            echo "Response [{$this->requestStatusCode}]; Requests remaining [{$this->rateLimitRemaining}/{$this->rateLimit}]\n";

            if ($this->requestStatusCode === 429) {

                $this->rateLimitReset =  $this->response->getHeader('X-RateLimit-Reset')[0];
                $this->rateLimitRetryAfter = $this->response->getHeader('Retry-After')[0];

                $this->log->error("{$this->requestStatusCode} Rate Limited. Going to sleep for {$this->rateLimitRetryAfter} seconds [{$this->rateLimitReset}]");
                echo "{$this->requestStatusCode} Rate Limited. Going to sleep for {$this->rateLimitRetryAfter} seconds [{$this->rateLimitReset}]\n";
                $this->sleeping = true;
                sleep((int) $this->rateLimitRetryAfter+2);
                continue;
            }

            $this->responseBody = json_decode(
                (string) $this->response->getBody()
            );
            $this->lastPage = $this->responseBody->data->Page->pageInfo->lastPage;

            $node = $this->responseBody->data->Page->ANIME;
            foreach ($node as $nodeItem) {

                // todo calculate end reach probability if provided with required flag
                $this->database[] = Parser\AnimeParser::parse($nodeItem);
            }

            if (count($this->database) >= $this->autosave) {
                $this->push();

                $this->database = [];
                $this->endReachProbabilityCount = 0;
            }

            $this->page++;
        }
    }

    private function exists($id)
    {
        $found = $this->mongodb->hiyori->processing_queue->findOne([
            'anilist_id' => $id
        ]);

        return $found === null ? false : true;
    }

    private function push()
    {
        $count = count($this->database);
        echo "Pushing {$count} document(s) to Database\n";
        $this->log->info("Pushing {$count} document(s) to Database");

        $dump = [];

        foreach ($this->database as $anime) {

            // Check if exists in MongoDB
            if ($this->exists($anime->getId())) {

                $this->log->info("Skipping {$anime->getId()}; already in database");
                echo "Skipping {$anime->getId()}; already in database\n";

                continue;
            }

            $externalLinks = [];
            if (is_iterable($anime->getExternalLinks())) {
                foreach ($anime->getExternalLinks() as $link) {
                    $externalLinks[] = [
                        'id' => $link->getId(),
                        'site' => $link->getSite(),
                        'url' => $link->getUrl()
                        
                    ];
                }
            }

            $dump[] = [
                'anilist_id' => $anime->getId(),
                'mal_id' => $anime->getIdMal(),
                'title_english' => $anime->getTitleEnglish(),
                'title_native' => $anime->getTitleNative(),
                'title_romaji' => $anime->getTitleRomaji(),
                'episodes' => $anime->getEpisodes(),
                'start_date_year' => $anime->getStartDate()[0],
                'start_date_month' => $anime->getStartDate()[1],
                'start_date_day' => $anime->getStartDate()[2],
                'end_date_year' => $anime->getEndDate()[0],
                'end_date_month' => $anime->getEndDate()[1],
                'end_date_day' => $anime->getEndDate()[2],
                'season' => $anime->getSeason(),
                'year' => $anime->getYear(),
                'type' => $anime->getType(),
                'source' => $anime->getSource(),
                'external_links' => $externalLinks,
            ];
        }

        try {
            $result = $this->mongodb->hiyori->processing_queue->insertMany($dump);

            echo "Inserted {$result->getInsertedCount()} document(s)\n";
            $this->log->info("Inserted {$result->getInsertedCount()} document(s)");

        } catch (\Exception $e) {
            $this->log->error("Failed to push to Database. Dumping...");
            echo "Failed to push to Database. Dumping...\n";

            file_put_contents(
                time().'.json',
                json_encode($dump)
            );
        }
    }
}
