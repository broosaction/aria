<?php

namespace Core\Joi;


use Closure;
use Core\Joi\System\Contracts\IContainer;
use Core\Joi\System\Exceptions\QueryException;
use Pimple\Container;
use ReflectionClass;
use ReflectionException;

class AriaContainer extends Container implements IContainer
{
    /**
     * @param ReflectionClass $class the class to instantiate
     * @return \stdClass the created class
     * @suppress PhanUndeclaredClassInstanceof
     */
    private function buildClass(ReflectionClass $class)
    {
        $constructor = $class->getConstructor();
        if ($constructor === null) {
            return $class->newInstance();
        } else {
            $parameters = [];
            foreach ($constructor->getParameters() as $parameter) {
                $parameterClass = $parameter->getClass();

                // try to find out if it is a class or a simple parameter
                if ($parameterClass === null) {
                    $resolveName = $parameter->getName();
                } else {
                    $resolveName = $parameterClass->name;
                }

                try {
                    $builtIn = $parameter->hasType() && $parameter->getType()->isBuiltin();
                    $parameters[] = $this->query($resolveName, !$builtIn);
                } catch (QueryException $e) {
                    // Service not found, use the default value when available
                    if ($parameter->isDefaultValueAvailable()) {
                        $parameters[] = $parameter->getDefaultValue();
                    } else if ($parameterClass !== null) {
                        $resolveName = $parameter->getName();
                        $parameters[] = $this->query($resolveName);
                    } else {
                        throw $e;
                    }
                }
            }
            return $class->newInstanceArgs($parameters);
        }
    }


    /**
     * If a parameter is not registered in the container try to instantiate it
     * by using reflection to find out how to build the class
     * @param string $name the class name to resolve
     * @return \stdClass
     * @throws QueryException if the class could not be found or instantiated
     */
    public function resolve($name)
    {
        $baseMsg = 'Could not resolve ' . $name . '!';
        try {
            $class = new ReflectionClass($name);
            if ($class->isInstantiable()) {
                return $this->buildClass($class);
            } else {
                throw new QueryException($baseMsg .
                    ' Class can not be instantiated');
            }
        } catch (ReflectionException $e) {
            throw new QueryException($baseMsg . ' ' . $e->getMessage());
        }
    }

    public function query(string $name, bool $autoload = true)
    {
        $name = $this->sanitizeName($name);
        if ($this->offsetExists($name)) {
            return $this->offsetGet($name);
        } else if ($autoload) {
            $object = $this->resolve($name);
            $this->registerService($name, function () use ($object) {
                return $object;
            });
            return $object;
        }
        throw new QueryException('Could not resolve ' . $name . '!');
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function registerParameter($name, $value)
    {
        $this[$name] = $value;
    }

    /**
     * The given closure is call the first time the given service is queried.
     * The closure has to return the instance for the given service.
     * Created instance will be cached in case $shared is true.
     *
     * @param string $name name of the service to register another backend for
     * @param Closure $closure the closure to be called on service creation
     * @param bool $shared
     */
    public function registerService($name, Closure $closure, $shared = true)
    {
        $name = $this->sanitizeName($name);
        if (isset($this[$name])) {
            unset($this[$name]);
        }
        if ($shared) {
            $this[$name] = $closure;
        } else {
            $this[$name] = $this->factory($closure);
        }
    }

    /**
     * Shortcut for returning a service from a service under a different key,
     * e.g. to tell the container to return a class when queried for an
     * interface
     * @param string $alias the alias that should be registered
     * @param string $target the target that should be resolved instead
     */
    public function registerAlias($alias, $target)
    {
        $this->registerService($alias, function (IContainer $container) use ($target) {
            return $container->query($target);
        }, false);
    }

    /*
     * @param string $name
     * @return string
     */
    protected function sanitizeName($name)
    {
        if (isset($name[0]) && $name[0] === '\\') {
            return ltrim($name, '\\');
        }
        return $name;
    }
}