<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/proophsoftware/prooph-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/proophsoftware/prooph-package/blob/master/LICENSE.md New BSD License
 */

return [
    'invokables' => [
        \Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy::class => \Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy::class,
        // Aggregate Translator
        \Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator::class => \Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator::class,
    ],
    'factories' => [
        // prooph/event-store set up
        \Prooph\EventStore\EventStore::class => \Prooph\EventStore\Container\EventStoreFactory::class,
        'Prooph\\EventStore\\Adapter\\Doctrine\\DoctrineEventStoreAdapter' => 'Prooph\\EventStore\\Adapter\\Doctrine\\Container\\DoctrineEventStoreAdapterFactory',
        // prooph snapshot setup
        \Prooph\Snapshotter\SnapshotPlugin::class => \Prooph\Snapshotter\Container\SnapshotPluginFactory::class,
        \Prooph\Snapshotter\Snapshotter::class => \Prooph\Snapshotter\Container\SnapshotterFactory::class,
        \Prooph\EventStore\Snapshot\SnapshotStore::class => \Prooph\EventStore\Container\Snapshot\SnapshotStoreFactory::class,
        'Prooph\\EventStore\\Snapshot\\Adapter\\Doctrine\\DoctrineSnapshotAdapter' => 'Prooph\\EventStore\\Snapshot\\Adapter\\Doctrine\\Container\\DoctrineSnapshotAdapterFactory',
        // prooph/service-bus set up
        \Prooph\ServiceBus\CommandBus::class => \Prooph\ServiceBus\Container\CommandBusFactory::class,
        \Prooph\ServiceBus\EventBus::class   => \Prooph\ServiceBus\Container\EventBusFactory::class,
        // prooph/event-store-bus-bridge set up
        \Prooph\EventStoreBusBridge\TransactionManager::class => \Prooph\EventStoreBusBridge\Container\TransactionManagerFactory::class,
        \Prooph\EventStoreBusBridge\EventPublisher::class     => \Prooph\EventStoreBusBridge\Container\EventPublisherFactory::class,
        // your factories
    ]
];
