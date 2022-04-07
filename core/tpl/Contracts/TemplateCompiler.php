<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 1:55
 */

namespace Core\tpl\Contracts;


interface TemplateCompiler
{


    /**
     * Get the evaluated view contents for the given view.
     *
     * @param array $data
     * @param array $mergeData
     */
    public function make($data = [], $mergeData = []);

    /**
     * Add a piece of shared data to the environment.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function saveCache();

    /**
     * Add a piece of shared data to the environment.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function getFromCache();

    /**
     * Register a view composer event.
     *
     * @param $name
     * @param $callback
     */
    public function composer($name, $callback);

    /**
     * Register a view composer event.
     */
    public function compose();

    /**
     * @param bool $replaceCache
     */
    public function render($replaceCache = false);

    /**
     * @param string $file
     */
    public function loadView($file = '');


    /**
     * Register a view composer event.
     * @param $callback
     */
    public function runCallback($callback);

    /**
     *
     * @param $key
     */
    public function __isset($key);

    /**
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value);


    /**
     *
     * @param $key
     */
    public function __get($key);


    /**
     *
     * @param $name
     * @param $args
     */
    public function __call($name, $args);

}