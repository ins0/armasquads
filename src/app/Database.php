<?php

declare(strict_types=1);

namespace Armasquads;

use PDO;

final class Database
{
    public static function connect(Config $config): PDO
    {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8', $config->dbHost, $config->dbName);

        return new PDO($dsn, $config->dbUser, $config->dbPassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);
    }
}
