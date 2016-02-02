<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/proophsoftware/prooph-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/proophsoftware/prooph-package/blob/master/LICENSE.md New BSD License
 */

namespace Prooph\Package\Container;

use Interop\Container\ContainerInterface;

/**
 * Factory for instantiating classes with no dependencies
 */
final class InvokableFactory
{
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        return new $requestedName;
    }
}
