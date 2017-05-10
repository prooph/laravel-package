<?php

declare(strict_types=1);
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

use \Prooph\Package\Container\InvokableFactory;

// list of services with callable container-interop factories
return [
    // prooph/event-store set up
    \Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator::class => InvokableFactory::class,
    \Prooph\EventStore\EventStore::class => \Prooph\EventStore\Pdo\Container\MySqlEventStoreFactory::class,
    // prooph/service-bus set up
    \Prooph\ServiceBus\CommandBus::class => \Prooph\ServiceBus\Container\CommandBusFactory::class,
    \Prooph\ServiceBus\EventBus::class => \Prooph\ServiceBus\Container\EventBusFactory::class,
    \Prooph\ServiceBus\QueryBus::class => \Prooph\ServiceBus\Container\QueryBusFactory::class,
    \Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy::class => InvokableFactory::class,
    // prooph/event-store-bus-bridge set up
    \Prooph\EventStoreBusBridge\TransactionManager::class => \Prooph\EventStoreBusBridge\Container\TransactionManagerFactory::class,
    \Prooph\EventStoreBusBridge\EventPublisher::class => \Prooph\EventStoreBusBridge\Container\EventPublisherFactory::class,
    // your factories
];
