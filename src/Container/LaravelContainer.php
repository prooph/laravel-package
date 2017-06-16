<?php

declare(strict_types=1);
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016-2017 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

namespace Prooph\Package\Container;

use Illuminate\Contracts\Container\Container;
use Psr\Container\ContainerInterface;
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

    private function hasIsCached(string $id): bool
    {
        return array_key_exists($id, $this->cacheForHas);
    }

    private function hasFromCache(string $id)
    {
        return $this->cacheForHas[$id];
    }

    private function cacheHas(string $id, bool $has)
    {
        $this->cacheForHas[$id] = $has;
    }

    private function isInstantiable(string $id): bool
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
