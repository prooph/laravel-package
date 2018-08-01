# Laravel package for prooph components

## Overview
This is a Laravel package for *prooph components* to get started out of the box with message bus, CQRS, event sourcing 
and snapshots. It uses the `prooph/pdo-event-store` event store however there are more adapters available.

It provides all [service definitions and a default configuration](config "Laravel Package Resources"). This is more like 
a Quick-Start package. If you want to use the prooph components in production, we recommend to configure the 
*prooph components* for your requirements. See the [documentation](http://getprooph.org/ "prooph components documentation") 
for more details of the *prooph components*.

For rapid prototyping we recommend to use our 
[prooph-cli](https://github.com/proophsoftware/prooph-cli "prooph command line interface") tool.

### Available services
* `Prooph\ServiceBus\CommandBus`: Dispatches commands
* `Prooph\ServiceBus\EventBus`: Dispatches events
* `Prooph\ServiceBus\QueryBus`: Allows for querying over a message bus.
* `Prooph\EventStoreBusBridge\TransactionManager`: Transaction manager for service bus and event store
* `Prooph\EventStoreBusBridge\EventPublisher`: Publishes events on the event bus

### Available event stores
* `Prooph\EventStore\Pdo\MariaDbEventStore`: MariaDB event store adapter
* `Prooph\EventStore\Pdo\MySqlEventStore`: MySQL event store adapter
* `Prooph\EventStore\Pdo\PostgresEventStore`: PostgreSQL event store adapter

### Available facades
* `CommandBus`: Usage: https://github.com/prooph/laravel-package/blob/master/examples/command_bus.php
* `EventBus`: Usage: https://github.com/prooph/laravel-package/blob/master/examples/event_bus.php
* `QueryBus`: Usage: https://github.com/prooph/laravel-package/blob/master/examples/query_bus.php


## Installation
You can install `prooph/laravel-package` via Composer by adding `"prooph/laravel-package": "^0.4"` 
as requirement to your composer.json. 

### Service Provider

If you are using Laravel 5.5 or higher the package will automatically register itself. Otherwise you need to add `Prooph\Package\ProophServiceProvider` to your 
[providers](https://laravel.com/docs/master/providers#registering-providers "Visit Laravel Documentation") array. 
Then you will have access to the services above.

This package has configuration files which can be configured to your needs.

Deploy the prooph config files to add your configuration for the prooph components.

```bash 
$ php artisan vendor:publish
```

### Database
Setup your [database migrations](https://github.com/prooph/event-store-doctrine-adapter#database-set-up)
for the Event Store and Snapshot with:

```bash
$ php artisan make:migration create_event_stream_table
```

Update the class `CreateEventStreamTable`:

```php
class CreateEventStreamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Prooph\Package\Migration\Schema\EventStoreSchema::createSingleStream('event_stream', true);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Prooph\Package\Migration\Schema\EventStoreSchema::dropStream('event_stream');
    }
}
```

And now for the snapshot table.

```bash
$ php artisan make:migration create_snapshot_table
```

Update the class `CreateSnapshotTable`:

```php
class CreateSnapshotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Prooph\Package\Migration\Schema\SnapshotSchema::create('snapshot');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Prooph\Package\Migration\Schema\SnapshotSchema::drop('snapshot');
    }
}
```

Now it's time to execute the migrations:

```bash
$ php artisan migrate
```

## Example
You have only to define your models (Entities, Repositories) and commands / routes. Here is an example config
from the [proophessor-do example app](https://github.com/prooph/proophessor-do "prooph components in action").

Define the aggregate repository, command route and event route for `RegisterUser` in `config/prooph.php`.
 
```php
// add the following config in your config/prooph.php under the specific config key
return [
    'event_store' => [
        // list of aggregate repositories
        'user_collection' => [
            'repository_class' => \Prooph\ProophessorDo\Infrastructure\Repository\EventStoreUserCollection::class,
            'aggregate_type' => \Prooph\ProophessorDo\Model\User\User::class,
            'aggregate_translator' => \Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator::class,
            'snapshot_store' => \Prooph\EventStore\Snapshot\SnapshotStore::class,
        ],
    ],
    'service_bus' => [
        'command_bus' => [
            'router' => [
                'routes' => [
                    // list of commands with corresponding command handler
                    \Prooph\ProophessorDo\Model\User\Command\RegisterUser::class => \Prooph\ProophessorDo\Model\User\Handler\RegisterUserHandler::class,
                ],
            ],
        ],
        'event_bus' => [
            'router' => [
                'routes' => [
                    // list of events with a list of projectors
                    \Prooph\ProophessorDo\Model\User\Event\UserWasRegistered::class => [
                        \Prooph\ProophessorDo\Projection\User\UserProjector::class
                    ],
                ],
            ],
        ],
    ],
];
```

Add the service container factories to `config/dependencies.php`.

```php
// add the following config in your config/dependencies.php after the other factories
return [
    // your factories
    // Model
    \Prooph\ProophessorDo\Model\User\Handler\RegisterUserHandler::class => \Prooph\ProophessorDo\Container\Model\User\RegisterUserHandlerFactory::class,
    \Prooph\ProophessorDo\Model\User\UserCollection::class => \Prooph\ProophessorDo\Container\Infrastructure\Repository\EventStoreUserCollectionFactory::class,
    // Projections
    \Prooph\ProophessorDo\Projection\User\UserProjector::class => \Prooph\ProophessorDo\Container\Projection\User\UserProjectorFactory::class,
    \Prooph\ProophessorDo\Projection\User\UserFinder::class => \Prooph\ProophessorDo\Container\Projection\User\UserFinderFactory::class,
];
```

Here is an example how to call the `RegisterUser` command:

```php
    /* @var $container \Illuminate\Container\Container */
    
    /* @var $commandBus \Prooph\ServiceBus\CommandBus */
    $commandBus = $container->make(Prooph\ServiceBus\CommandBus::class);

    $command = new \Prooph\ProophessorDo\Model\User\Command\RegisterUser(
        [
            'user_id' => \Rhumsaa\Uuid\Uuid::uuid4()->toString(),
            'name' => 'prooph',
            'email' => 'my@domain.com',
        ]
    );

    $commandBus->dispatch($command);
```

Here is an example how to get a list of all users from the example above:

```php
    /* @var $container \Illuminate\Container\Container */
    $userFinder = $container->make(Prooph\ProophessorDo\Projection\User\UserFinder::class);

    $users = $userFinder->findAll();
```

## Support

- Ask questions on Stack Overflow tagged with [#prooph](https://stackoverflow.com/questions/tagged/prooph).
- File issues at [https://github.com/prooph/laravel-package/issues](https://github.com/prooph/laravel-package/issues).
- Say hello in the [prooph gitter](https://gitter.im/prooph/improoph) chat.

## Contribute

Please feel free to fork and extend existing or add new plugins and send a pull request with your changes!
To establish a consistent code quality, please provide unit tests for all your changes and may adapt the documentation.

## License

Released under the [New BSD License](LICENSE.md).
