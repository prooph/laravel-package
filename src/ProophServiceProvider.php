<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Prooph\Package;

use Illuminate\Support\ServiceProvider;
use Prooph\Package\Container\LaravelContainer;

/**
 * Laravel service provider for prooph components
 */
final class ProophServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $path = $this->getConfigPath();

        $this->publishes(
            [
                $path . '/prooph.php' => config_path('prooph.php'),
                $path . '/dependencies.php' => config_path('dependencies.php'),
            ],
            'config');
    }

    /**
     * @return string
     */
    private function getConfigPath()
    {
        return dirname(__DIR__) . '/config';
    }

    /**
     * @interitdoc
     */
    public function register()
    {
        $path = $this->getConfigPath();

        $this->mergeConfigFrom(
            $path . '/prooph.php', 'prooph'
        );
        $this->mergeConfigFrom(
            $path . '/dependencies.php', 'dependencies'
        );

        $this->app->singleton(LaravelContainer::class, function ($app) {
            return new LaravelContainer($app);
        });

        foreach (config('dependencies') as $service => $factory) {
            $this->app->singleton($factory, function () use ($factory) {
                return new $factory();
            });
            $this->app->singleton($service, function ($app) use ($service, $factory) {
                return $app->make($factory)->__invoke($app->make(LaravelContainer::class), $service);
            });
        }

        $this->app->singleton('laravel.connections.pdo', function ($app) {
            return $app['database']->connection()->getPdo();
        });
    }

    /**
     * @interitdoc
     */
    public function provides()
    {
        return [
            // service bus
            \Prooph\ServiceBus\CommandBus::class,
            \Prooph\ServiceBus\EventBus::class,
            \Prooph\ServiceBus\QueryBus::class,
            // event-store-bus-bridge
            \Prooph\EventStoreBusBridge\TransactionManager::class,
            \Prooph\EventStoreBusBridge\EventPublisher::class,
            // event store
            \Prooph\EventStore\EventStore::class,
            \Prooph\EventStore\Pdo\MySqlEventStore::class,
        ];
    }
}
