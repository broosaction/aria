<?php

namespace Core\Drivers\ContentPlug;


use Core\Joi\System\Contracts\IData;

class Data implements IData
{

    /**
     * @var IData
     */
    private $mechnism;

    /**
     * @var array
     */
    private static $registers = array();

    /**
     * @var bool
     */
    private static $initial = false;

    /**
     * Data constructor.
     *
     * @param string $mechnism the mechnism to use
     * @param array $credinals
     * @return void
     */
    public function __construct($mechnism, array $credinals = array())
    {
        if (!static::$initial)
            static::fireOnInitial();

        if (!array_key_exists($mechnism, static::$registers) || empty(static::$registers[$mechnism]))
            throw new \InvalidArgumentException("$mechnism Mechnism has not been registered");

        $mechnism = static::$registers[$mechnism];
        $this->mechnism = new $mechnism($credinals);
    }

    /**
     * Register Data Mechnism
     *
     * @param string $mechnism
     * @param string $class
     * @return void
     */
    public static function register($mechnism, $class)
    {
        static::$registers[$mechnism] = $class;
    }

    /**
     * Fire the initial job
     */
    public static function fireOnInitial()
    {
        $classes = array(
            'apc'       =>'Sse\\Mechnisms\\ApcMechnism',
            'file'      => 'Sse\\Mechnisms\\FileMechnism',
            'memcache'  => 'Sse\\Mechnisms\\MemcacheMechnism',
            'mongo'     => 'Sse\\Mechnisms\\MongoMechnism',
            'pdo'       => 'Sse\\Mechnisms\\PdoMechnism',
            'redis'     => 'Sse\\Mechnisms\\RedisMechnism',
            'xcache'    => 'Sse\\Mechnisms\\XCacheMechnism',
        );

        foreach ($classes as $class => $mechnism) {
            static::register($class, $mechnism);
        }
    }

    public function get($key)
    {
        return $this->mechnism->get($key);
    }

    public function set($key, $value)
    {
        return $this->mechnism->set($key, $value);
    }

    public function delete($key)
    {
        return $this->mechnism->delete($key);
    }

    public function has($key)
    {
        return $this->mechnism->has($key);
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function __unset($name)
    {
        $this->delete($name);
    }

    public function __isset($key)
    {
        return $this->has($key);
    }
}