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
use Prooph\ServiceBus\QueryBus;
use React\Promise\Promise;

final class QueryBusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_will_proxy_calls_from_facade_to_instance_on_application()
    {
        $app = new Application();
        $stub = static::prophesize(QueryBus::class);

        $app[QueryBus::class] = $stub->reveal();

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        $stub->dispatch('DidTestPass')->shouldBeCalled();

        \Prooph\Package\Facades\QueryBus::dispatch('DidTestPass');
    }

    /**
     * @test
     */
    public function it_can_return_the_result_fine()
    {
        $app = new Application();
        $stub = static::prophesize(QueryBus::class);

        $app[QueryBus::class] = $stub->reveal();

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        $promise = new Promise(function ($resolve) {
            $resolve('abc');
        }, function () {
        });
        $stub->dispatch('DidTestPass')->willReturn($promise);

        $result = \Prooph\Package\Facades\QueryBus::resultFrom('DidTestPass');

        static::assertEquals('abc', $result);
    }

    /**
     * @test
     * @expectedException \Prooph\Package\Exception\QueryResultFailed
     */
    public function it_can_return_an_error_fine()
    {
        $app = new Application();
        $stub = static::prophesize(QueryBus::class);

        $app[QueryBus::class] = $stub->reveal();

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        $promise = new Promise(function ($_, $reject) {
            $reject('abc');
        }, function () {
        });
        $stub->dispatch('DidTestPass')->willReturn($promise);

        \Prooph\Package\Facades\QueryBus::resultFrom('DidTestPass');
    }
}
