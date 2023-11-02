<?php

namespace Core\Joi\System\Contracts;

interface IData
{
    /**
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value);

    /**
     * @param string $key
     */
    public function delete($key);

    /**
     * @param string $key
     * @return bool
     */
    public function has($key);
}