<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ProophTest\Package\Facade;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use Prooph\ServiceBus\CommandBus;

final class CommandBusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_will_proxy_calls_from_facade_to_instance_on_application()
    {
        $app = new Application();
        $stub = static::prophesize(CommandBus::class);

        $app[CommandBus::class] = $stub->reveal();

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        $stub->dispatch('AttemptToPassTest')->shouldBeCalled();

        \Prooph\Package\Facades\CommandBus::dispatch('AttemptToPassTest');
    }
}
