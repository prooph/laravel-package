<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/proophsoftware/prooph-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/proophsoftware/prooph-package/blob/master/LICENSE.md New BSD License
 */

namespace Prooph\Package;

use Doctrine\DBAL\DriverManager;
use Illuminate\Support\ServiceProvider;
use Interop\Container\ContainerInterface;

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

    private function getConfigPath()
    {
        return dirname(__DIR__) . '/config';
    }

    /**
     * Register any package services.
     *
     * @return void
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

        foreach (config('dependencies') as $type => $services) {
            foreach ($services as $service => $factory) {
                switch ($type) {
                    case 'invokables':
                        $this->app->singleton($service, function () use ($factory) {
                            return new $factory();
                        });
                        break;
                    case 'factories':
                        $this->app->singleton($factory, function () use ($factory) {
                            return new $factory();
                        });
                        $this->app->singleton($service, function ($app) use ($factory) {
                            return $app->make($factory)->__invoke($app->make(ContainerInterface::class));
                        });
                        break;
                    default:
                        // nothing to do
                        break;
                }
            }
        }

        $this->app->singleton('doctrine.connection.default', function () {
            return DriverManager::getConnection(config('doctrine')['connection']['default']);
        });
    }

    public function provides()
    {
        return [
            \Prooph\EventStore\EventStore::class,
            \Prooph\EventStore\Snapshot\SnapshotStore::class,
            // service bus
            \Prooph\ServiceBus\CommandBus::class,
            \Prooph\ServiceBus\EventBus::class,
            \Prooph\EventStore\Adapter\Doctrine\DoctrineEventStoreAdapter::class,
            \Prooph\EventStore\Snapshot\Adapter\Doctrine\DoctrineSnapshotAdapter::class,
            // prooph/event-store-bus-bridge set up
            \Prooph\EventStoreBusBridge\TransactionManager::class,
            \Prooph\EventStoreBusBridge\EventPublisher::class,
            'Prooph\\EventStore\\Adapter\\Doctrine\\DoctrineEventStoreAdapter',

        ];
    }
}
