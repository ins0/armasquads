<?php

declare(strict_types=1);

use Armasquads\Bootstrap;
use Armasquads\Config;

require __DIR__ . '/../vendor/autoload.php';

Bootstrap::createApp(Config::fromEnvironment(dirname(__DIR__)))->run();
