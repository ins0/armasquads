Armasquads.com
=======================

Introduction
------------
Website for creating Squad XML files in ArmA based games

## Installation

### Main Setup

 * Clone this project `git clone git://github.com/ins0/armasquads.git`
 * Run `docker compose up -d --build`; the website is available on http://localhost:8080
 * On the first start MariaDB creates the tables from `docker/mariadb/init.sql`

The application is a small [Slim 4](https://www.slimframework.com/) app in `src/` and runs on PHP 8.4. Squad logos are
converted to PAA files with [armake](https://github.com/KoffeinFlummi/armake), which is built into the Docker image.

### Tests

```
cd src
composer install
vendor/bin/phpunit
```

Tests that need the imagick extension or armake are skipped when they are missing. Inside the Docker image all tests run.

RESTful API SDK
------------
https://github.com/ins0/armasquads-api-restclient
