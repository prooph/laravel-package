<?php

declare(strict_types=1);
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */
// default example configuration for prooph components, see http://getprooph.org/
return [
    'event_store' => [
        'adapter' => [
            'type' => \Prooph\EventStore\Pdo\MySqlEventStore::class,
            'options' => [
                'connection_alias' => 'laravel.connections.pdo',
            ],
        ],
        'plugins' => [
            \Prooph\EventStoreBusBridge\EventPublisher::class,
            \Prooph\EventStoreBusBridge\TransactionManager::class,
        ],
        // list of aggregate repositories
    ],
    'service_bus' => [
        'command_bus' => [
            'router' => [
                'routes' => [
                    // list of commands with corresponding command handler
                ],
            ],
        ],
        'event_bus' => [
            'plugins' => [
                \Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy::class,
            ],
            'router' => [
                'routes' => [
                    // list of events with a list of projectors
                ],
            ],
        ],
    ],
    'snapshot_store' => [
        'adapter' => [
            'type' => \Prooph\SnapshotStore\Pdo\PdoSnapshotStore::class,
            'options' => [
                'connection_alias' => 'laravel.connections.pdo',
                'snapshot_table_map' => [
                    // list of aggregate root => table (default is snapshot)
                ],
            ],
        ],
    ],
    'snapshotter' => [
        'version_step' => 5, // every 5 events a snapshot
        'aggregate_repositories' => [
            // list of aggregate root => aggregate repositories
        ],
    ],
];
