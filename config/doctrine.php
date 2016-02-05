<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

return [
    'connection' => [
        'default' => [
            'driverClass' => \Doctrine\DBAL\Driver\PDOMySql\Driver::class,
            'host' => env('DB_HOST', 'localhost'),
            'port' => '3306',
            'user' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'dbname' => env('DB_DATABASE', 'forge'),
            'charset' => 'utf8',
            'driverOptions' => [
                1002 => "SET NAMES 'UTF8'"
            ],
        ],
    ],
 ];
