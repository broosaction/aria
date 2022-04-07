<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 1:01
 */

namespace Core\tpl\Contracts;


interface TemplateEngine
{


    /**
     * Get the evaluated view contents for the given path.
     *
     * @param string $path
     * @param array $data
     * @param array $mergeData
     */
    public function file($path, $data = [], $mergeData = []);

    /**
     * Gets the theme for the
     *
     * @param $theme
     */
    public function setTheme($theme);

    /**
     * Gets the theme for the
     *
     */
    public function getTemplateEngineName();

    /**
     * Gets the theme for the
     *
     */
    public function getTemplateEngineVersion();

    /**
     *
     * @param $view
     * @param bool $cache
     * @param bool $replaceCache
     * @param null $callback
     */
    public function render($view, $cache = false, $replaceCache = false, $callback = null);

    public function getCompiler();

    /**
     *
     * @param $key
     * @return $this
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

    public function _init(): void;
}