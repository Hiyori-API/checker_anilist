<?php

namespace CheckerAnilist\Parser;

use CheckerAnilist\Model\Anime;
use CheckerAnilist\Model\ExternalLink;

class AnimeParser
{
    public static function parse(object $response) : Anime
    {
        $model = new Anime();

        $model->setId($response->id);
        $model->setIdMal($response->idMal);
        $model->setTitleEnglish($response->title->english);
        $model->setTitleNative($response->title->native);
        $model->setTitleRomaji($response->title->romaji);
        $model->setEpisodes($response->episodes);

        $model->setStartDate(
            $response->startDate->year,
            $response->startDate->month,
            $response->startDate->day
        );

        $model->setEndDate(
            $response->endDate->year,
            $response->endDate->month,
            $response->endDate->day
        );

        $model->setSeason(
            strtolower($response->season)
        );
        $model->setYear($response->seasonYear);
        $model->setType($response->format);
        $model->setSource($response->source);

        if (!empty($response->externalLinks)) {

            $externalLinks = function($extLinks) {
                foreach ($extLinks as $link) {
                    yield ExternalLink::new($link->id, $link->site, $link->url);
                }
            };

            $model->setExternalLinks(
                $externalLinks($response->externalLinks)
            );
        }

        return $model;
    }
}