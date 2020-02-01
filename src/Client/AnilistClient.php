<?php

namespace CheckerAnilist\Client;

use GuzzleHttp\Client;

class AnilistClient
{
    private $request;
    private $client;

    public function __construct($client = null)
    {
        $this->client = $client ?? new Client();
    }

    public function request(string $query, array $variables) {
        return $this->client->post($_ENV['ANILIST_API_URL'], [
            'json' => [
                'query' => $query,
                'variables' => $variables
            ]
        ]);
    }
}