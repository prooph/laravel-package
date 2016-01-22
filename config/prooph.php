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
        ],
        // list of aggregate repositories
    ],
    'service_bus' => [
        'command_bus' => [
            'router' => [
                'routes' => [
                    // list of commands with corresponding command handler
                ]
            ]
        ],
        'event_bus' => [
            'plugins' => [
                \Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy::class
            ],
            'router' => [
                'routes' => [
                    // list of events with a list of projectors
                ]
            ]
        ]
    ]
];
