<?php
return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [
                    'host'     => '***LIVE_DB_HOST***',
                    'port'     => '3306',
                    'user'     => '***LIVE_DB_USERNAME***',
                    'password' => '***LIVE_DB_PASSWORD***',
                    'dbname'   => '***LIVE_DB_NAME***',
                    'charset'  => 'utf8',
                    'driver_options' => [
                        \PDO::MYSQL_ATTR_INIT_COMMAND       => 'SET NAMES utf8',
                    ],
                ],
            ],
        ],
    ],
];
