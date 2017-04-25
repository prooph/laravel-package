<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

namespace Prooph\Package\Container;

use Illuminate\Contracts\Container\Container;
use Interop\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;

final class LaravelContainer implements ContainerInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var array
     */
    private $cacheForHas = [];

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->container->make($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        if ($this->hasIsCached($id)) {
            return $this->hasFromCache($id);
        }

        $has = $this->container->bound($id) || $this->isInstantiable($id);

        $this->cacheHas($id, $has);

        return $has;
    }

    private function hasIsCached($id)
    {
        return array_key_exists($id, $this->cacheForHas);
    }

    private function hasFromCache($id)
    {
        return $this->cacheForHas[$id];
    }

    private function cacheHas($id, $has)
    {
        $this->cacheForHas[$id] = $has;
    }

    private function isInstantiable($id)
    {
        if (class_exists($id)) {
            return true;
        }

        try {
            $reflectionClass = new ReflectionClass($id);

            return $reflectionClass->isInstantiable();
        } catch (ReflectionException $e) {
            return false;
        }
    }
}
