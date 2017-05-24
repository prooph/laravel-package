<?php

declare(strict_types=1);
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

namespace Prooph\Package\Container;

use Psr\Container\ContainerInterface;

/**
 * Factory for instantiating classes with no dependencies
 */
final class InvokableFactory
{
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        return new $requestedName();
    }
}
