<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace {

    use Prooph\Common\Event\ActionEventEmitter;
    use Prooph\Common\Event\ActionEventListenerAggregate;
    use React\Promise\Promise;

    /**
     * @see \Prooph\ServiceBus\CommandBus
     */
    final class CommandBus
    {
        public static function dispatch($command): void
        {
        }

        public static function utilize(ActionEventListenerAggregate $plugin): void
        {
        }

        public static function setActionEventEmitter(ActionEventEmitter $actionEventDispatcher): void
        {
        }

        public static function getActionEventEmitter(): ActionEventEmitter
        {
        }
    }

    /**
     * @see \Prooph\ServiceBus\EventBus
     */
    final class EventBus
    {
        public static function dispatch($event): void
        {
        }

        public static function utilize(ActionEventListenerAggregate $plugin): void
        {
        }

        public static function setActionEventEmitter(ActionEventEmitter $actionEventDispatcher): void
        {
        }

        public static function getActionEventEmitter(): ActionEventEmitter
        {
        }
    }

    /**
     * @see \Prooph\ServiceBus\QueryBus
     */
    final class QueryBus
    {
        public static function dispatch($query): Promise
        {
        }

        /**
         * @return mixed
         */
        public static function resultFrom($aQuery)
        {
        }

        public static function utilize(ActionEventListenerAggregate $plugin): void
        {
        }

        public static function setActionEventEmitter(ActionEventEmitter $actionEventDispatcher): void
        {
        }

        public static function getActionEventEmitter(): ActionEventEmitter
        {
        }
    }

}
