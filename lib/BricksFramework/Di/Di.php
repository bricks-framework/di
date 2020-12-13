<?php

/** @copyright Sven Ullmann <kontakt@sumedia-webdesign.de> **/

namespace BricksFramework\Di;

use BricksFramework\Container\PsrContainer;

class Di implements DiInterface
{
    protected $factories = [];

    protected $arguments = [];

    /** @var PsrContainer */
    protected $container;

    public function __construct(PsrContainer $container)
    {
        $this->container = $container;
    }

    public function setFactory($class, callable $factory) : void
    {
        $this->factories[$class] = $factory;
    }

    public function hasFactory($class) : bool
    {
        return isset($this->factories[$class]);
    }

    public function getFactory($class) : callable
    {
        return $this->factories[$class] ?? $this->getDefaultFactory();
    }

    protected function getDefaultFactory() : callable
    {
        return function($container, $class, $arguments) {
            return new $class(...array_values($arguments));
        };
    }

    public function setArgument(string $class, int $argNum, $argument) : void
    {
        $this->arguments[$class][$argNum] = $argument;
    }

    public function hasArgument(string $class, int $argNum) : bool
    {
        return isset($this->arguments[$class][$argNum]);
    }

    public function getArguments(string $class) : array
    {
        return $this->arguments[$class] ?? [];
    }

    public function hasArguments(string $class) : bool
    {
        return (bool) count($this->arguments[$class] ?? []);
    }

    public function get(string $class, array $arguments = [], $useFactory = true) : object
    {
        if (empty($arguments) && $this->hasArguments($class)) {
            $arguments = $this->getArguments($class);
        }

        $factory = $useFactory ? $this->getFactory($class) : $this->getDefaultFactory();

        return $factory($this->container, $class, $arguments);
    }
}
