<?php

namespace CheckerAnilist\Model;

/**
 * Class Anime
 * @package CheckerAnilist\Model
 */
class Anime
{
    /**
     * @var int
     *
     * Anilist's ID
     */
    private $id;
    /**
     * @var int
     *
     * MyAnimeList's ID (if exists; otherwise null)
     */
    private $idMal;
    /**
     * @var string
     */
    private $titleEnglish;
    /**
     * @var string
     */
    private $titleRomaji;
    /**
     * @var string
     */
    private $titleNative;
    /**
     * @var int
     */
    private $episodes;
    /**
     * @var array
     */
    private $startDate;
    /**
     * @var array
     */
    private $endDate;
    /**
     * @var string
     */
    private $season;
    /**
     * @var int
     */
    private $year;
    /**
     * @var string
     *
     * e.g "TV", "OVA", "Movie", etc
     */
    private $type;
    /**
     * @var string
     *
     * e.g "Manga", "Novel", "Original", etc
     */
    private $source;

    /**
     * @var ExternalLink
     */
    private $externalLinks;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Anime
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdMal()
    {
        return $this->idMal;
    }

    /**
     * @param int $idMal
     * @return Anime
     */
    public function setIdMal($idMal)
    {
        $this->idMal = $idMal;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitleEnglish()
    {
        return $this->titleEnglish;
    }

    /**
     * @param string $titleEnglish
     * @return Anime
     */
    public function setTitleEnglish($titleEnglish)
    {
        $this->titleEnglish = $titleEnglish;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitleRomaji()
    {
        return $this->titleRomaji;
    }

    /**
     * @param string $titleRomaji
     * @return Anime
     */
    public function setTitleRomaji($titleRomaji)
    {
        $this->titleRomaji = $titleRomaji;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitleNative()
    {
        return $this->titleNative;
    }

    /**
     * @param string $titleNative
     * @return Anime
     */
    public function setTitleNative($titleNative)
    {
        $this->titleNative = $titleNative;
        return $this;
    }

    /**
     * @return int
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }

    /**
     * @param int $episodes
     * @return Anime
     */
    public function setEpisodes($episodes)
    {
        $this->episodes = $episodes;
        return $this;
    }

    /**
     * @return array
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param int|null $year
     * @param int|null $month
     * @param int|null $day
     * @return Anime
     */
    public function setStartDate(?int $year, ?int $month, ?int $day)
    {
        $this->startDate = [$year, $month, $day];
        return $this;
    }

    /**
     * @return array
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param int|null $year
     * @param int|null $month
     * @param int|null $day
     * @return Anime
     */
    public function setEndDate(?int $year, ?int $month, ?int $day)
    {
        $this->endDate = [$year, $month, $day];
        return $this;
    }

    /**
     * @return string
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param string $season
     * @return Anime
     */
    public function setSeason($season)
    {
        $this->season = $season;
        return $this;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     * @return Anime
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Anime
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return Anime
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return ExternalLink[]
     */
    public function getExternalLinks()
    {
        return $this->externalLinks;
    }

    /**
     * @param ExternalLink[] $externalLinks
     * @return Anime
     */
    public function setExternalLinks($externalLinks)
    {
        $this->externalLinks = $externalLinks;
        return $this;
    }
}