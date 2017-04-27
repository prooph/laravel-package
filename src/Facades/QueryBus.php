<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Prooph\Package\Facades;

use Illuminate\Support\Facades\Facade;
use Prooph\Common\Messaging\Query;
use Prooph\Package\Exception\QueryResultFailed;

final class QueryBus extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor()
    {
        return \Prooph\ServiceBus\QueryBus::class;
    }

    /**
     * The default dispatch method will return you a promise
     * sometimes you might want to just get the result instead
     * this method will return you the result on a successful
     * query, and throw an exception instead if the query
     * fails.
     *
     * @throws QueryResultFailed
     * @param Query|mixed $aQuery
     * @return mixed
     */
    public static function resultFrom($aQuery)
    {
        /** @var \Prooph\ServiceBus\QueryBus $instance */
        $instance = self::getFacadeRoot();
        $ret = null;

        $instance
            ->dispatch($aQuery)
            ->done(function ($result) use (&$ret) {
                $ret = $result;
            }, function () use ($aQuery) {
                throw QueryResultFailed::fromQuery($aQuery, func_get_args());
            });

        return $ret;
    }
}
