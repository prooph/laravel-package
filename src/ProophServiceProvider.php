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
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
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
    public function boot(Filesystem $filesystem)
    {
        $path = $this->getConfigPath();

        $this->publishes(
            [
                $path . '/prooph.php' => config_path('prooph.php'),
                $path . '/dependencies.php' => config_path('dependencies.php'),
            ],
            'config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_event_stream_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_event_stream_table.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../database/migrations/create_snapshot_table.php.stub' => $this->getMigrationFileName($filesystem, 'create_snapshot_table.php'),
        ], 'migrations');
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

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param Filesystem $filesystem
     * @return string
     */
    protected function getMigrationFileName(Filesystem $filesystem, string $filename): string
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $filename) {
                return $filesystem->glob($path.'*_'.$filename);
            })->push($this->app->databasePath()."/migrations/{$timestamp}_".$filename)
            ->first();
    }
}
