<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

namespace Prooph\Package;

use Doctrine\DBAL\DriverManager;
use Illuminate\Support\ServiceProvider;
use Interop\Container\ContainerInterface;

/**
 * Laravel service provider for prooph components
 */
class ProophServiceProvider extends ServiceProvider
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
                $path . '/doctrine.php' => config_path('doctrine.php'),
                $path . '/dependencies.php' => config_path('dependencies.php'),
            ],
            'config')
        ;
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
            $path . '/doctrine.php', 'doctrine'
        );
        $this->mergeConfigFrom(
            $path . '/dependencies.php', 'dependencies'
        );

        foreach (config('dependencies') as $service => $factory) {
            $this->app->singleton($factory, function () use ($factory) {
                return new $factory();
            });
            $this->app->singleton($service, function ($app) use ($service, $factory) {
                return $app->make($factory)->__invoke($app->make(ContainerInterface::class), $service);
            });
        }

        $this->app->singleton('doctrine.connection.default', function () {
            return DriverManager::getConnection(config('doctrine')['connection']['default']);
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
            // event-store-bus-bridge
            \Prooph\EventStoreBusBridge\TransactionManager::class,
            \Prooph\EventStoreBusBridge\EventPublisher::class,
            // event store
            \Prooph\EventStore\EventStore::class,
            \Prooph\EventStore\Snapshot\SnapshotStore::class,
            \Prooph\EventStore\Adapter\Doctrine\DoctrineEventStoreAdapter::class,
            \Prooph\EventStore\Snapshot\Adapter\Doctrine\DoctrineSnapshotAdapter::class,
        ];
    }
}
