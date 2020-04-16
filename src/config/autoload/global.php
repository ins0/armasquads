<?php

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [
                    'host'     => $_SERVER['MYSQL_HOST'],
                    'user'     => $_SERVER['MYSQL_USER'],
                    'password' => $_SERVER['MYSQL_PWD'],
                    'dbname'   => $_SERVER['MYSQL_DB'],
                    'charset'  => 'utf8',
                    'driver_options' => [
                        \PDO::MYSQL_ATTR_INIT_COMMAND       => 'SET NAMES utf8',
                    ],
                ],
            ],
        ],
    ],
];
