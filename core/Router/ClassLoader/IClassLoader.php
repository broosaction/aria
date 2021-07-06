<?php

namespace Core\Router\ClassLoader;

use Core\joi\Start;

interface IClassLoader
{

    /**
     * Called when loading class
     * @param string $class
     * @param Start $server
     * @return object
     */
    public function loadClass(string $class, Start $server);

    /**
     * Called when loading class method
     * @param object $class
     * @param string $method
     * @param array $parameters
     * @return object
     */
    public function loadClassMethod($class, string $method, array $parameters);

    /**
     * Called when loading method
     *
     * @param callable $closure
     * @param array $parameters
     * @return mixed
     */
    public function loadClosure(Callable $closure, array $parameters);

}