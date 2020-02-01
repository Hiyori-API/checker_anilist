# Checker AniList

A "Checker" is a microservice for the Hiyori DB API.

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