<?php

namespace Core\Router\ClassLoader;


use Core\Joi\Start;
use Core\Joi\System\Exceptions\ClassNotFoundHttpException;

class ClassLoader implements IClassLoader
{
    /**
     * Load class
     *
     * @param string $class
     * @param Start $server
     * @return object
     * @throws ClassNotFoundHttpException
     */
    public function loadClass(string $class,Start $server)
    {
        if (class_exists($class) === false) {
            throw new ClassNotFoundHttpException($class, null, sprintf('Class "%s" does not exist', $class), 404, null);
        }

        return new $class($server);
    }

    /**
     * Called when loading class method
     * @param object $class
     * @param string $method
     * @param array $parameters
     * @return object
     */
    public function loadClassMethod($class, string $method, array $parameters)
    {
        return call_user_func_array([$class, $method], array_values($parameters));
    }

    /**
     * Load closure
     *
     * @param Callable $closure
     * @param array $parameters
     * @return mixed
     */
    public function loadClosure(Callable $closure, array $parameters)
    {
        return call_user_func_array($closure, array_values($parameters));
    }

}