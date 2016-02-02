<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/proophsoftware/prooph-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/proophsoftware/prooph-package/blob/master/LICENSE.md New BSD License
 */

return [
    'event_store' => [
        'adapter' => [
            'type' => 'Prooph\EventStore\Adapter\Doctrine\DoctrineEventStoreAdapter',
            'options' => [
                'connection_alias' => 'doctrine.connection.default',
            ],
        ],
        'plugins' => [
            \Prooph\EventStoreBusBridge\EventPublisher::class,
            \Prooph\EventStoreBusBridge\TransactionManager::class,
            \Prooph\Snapshotter\SnapshotPlugin::class,
        ],
        // list of aggregate repositories
    ],
    'service_bus' => [
        'command_bus' => [
            'router' => [
                'routes' => [
                    \Prooph\Snapshotter\TakeSnapshot::class => \Prooph\Snapshotter\Snapshotter::class,
                    // list of commands with corresponding command handler
                ],
            ],
        ],
        'event_bus' => [
            'plugins' => [
                \Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy::class
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
            'type' => \Prooph\EventStore\Snapshot\Adapter\Doctrine\DoctrineSnapshotAdapter::class,
            'options' => [
                'connection_alias' => 'doctrine.connection.default',
                'snapshot_table_map' => [
                    // list of aggregate root => table (default is snapshot)
                ]
            ]
        ]
    ],
    'snapshotter' => [
        'version_step' => 5, // every 5 events a snapshot
        'aggregate_repositories' => [
            // list of aggregate root => aggregate repositories
        ]
    ],
];
