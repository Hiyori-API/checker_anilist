<?php

namespace CheckerAnilist\Model;


/**
 * Class ExternalLink
 * @package CheckerAnilist\Model
 */
class ExternalLink
{
    /**
     * @var int|string
     */
    private $id;
    /**
     * @var string
     */
    private $site;
    /**
     * @var string
     */
    private $url;

    /**
     * @param string $id
     * @param string $site
     * @param string $url
     * @return ExternalLink
     */
    public static function new(string $id, string $site, string $url) : self
    {
        $instance = new self();

        $instance->id = $id;
        $instance->site = $site;
        $instance->url = $url;

        return $instance;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}