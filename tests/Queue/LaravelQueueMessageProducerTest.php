<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ProophTest\Package\Queue;

use Illuminate\Foundation\Application;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;
use Prooph\ServiceBus\Async\AsyncMessage;
use Prooph\ServiceBus\CommandBus;
use Illuminate\Support\Facades\Facade;

class AsyncCommandSample extends Command implements AsyncMessage
{
    use PayloadTrait;
}

class LaravelQueueMessageProducerTest extends \PHPUnit_Framework_TestCase
{
    
    function test_producer_will_dispatch_async_command_through_async_job()
    {
        $app                    = new Application();
        $stub                   = static::prophesize(CommandBus::class);
        $app[CommandBus::class] = $stub->reveal();
        $app->bind('Illuminate\Contracts\Bus\Dispatcher', 'Illuminate\Bus\Dispatcher');
        
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
        
        $command  = new AsyncCommandSample(['name' => 'Joe']);
        $producer = new \Prooph\Package\Queue\LaravelQueueMessageProducer();
        
        $stub->dispatch($command)->shouldBeCalled();
        $producer->__invoke($command);
    }
    
}

