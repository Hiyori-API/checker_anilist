# Checker AniList

A "Checker" is a microservice for the Hiyori DB API.

Work in progress.

## Prerequisites
- PHP 7.2+
- MongoDB (or just the connection string)
- PHP JSON extension `ext-json`
- Composer - PHP Package manager

## Dependencies
- mongodb/mongodb: ^1.5
- guzzlehttp/guzzle: ^6.5
- symfony/dotenv: ^5.0
- monolog/monolog: ^2.0

## Installation
1. `git clone https://github.com/Hiyori-API/checker_anilist.git`
2. `composer install`
3. `cp .env.dist .env`
4. Set MongoDB connection string and configuration in `.env`


## Example Code

```php

require_once __DIR__.'/vendor/autoload.php';

$checker = new \CheckerAnilist\Anilist();
$checker->start();
```

## Features
1. Rate Limit detector
2. Some configuration

## Todo
1. Better configuration
2. Complete CLI-fication

## Metadata Model
1. AniList ID
2. MyAnimeList ID
3. Title English
4. Title Romaji
5. Title Native
6. Episodes
7. Start Date (YY/MM/DD)
8. End Date (YY/MM/DD)
9. Season
10. Year
11. Type (e.g "TV", "OVA", "Movie", etc)
12. Source (e.g "Manga", "Novel", "Original", etc)
13. External Links
    a. ID
    b. Site Title
    c. Site URl 

## Note
The meta data that is collected is required to generate a confidence score between multiple platforms for the same resource. This is required to generate a universal resource ID only and the final database will be excluding most of the data that has been parsed. All data that is collected is in public domain.