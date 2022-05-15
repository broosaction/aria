<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/13/2019
 * Time: 21:10
 */

namespace Core\tpl;


use Core\Config\Config;
use Core\joi\Start;
use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Compilers\BaseTemplateCompiler;
use Core\tpl\Contracts\TemplateEngine;
use Latte\Engine;


class Aria extends BaseTemplate implements TemplateEngine
{


    private AriaCompiler $AriaCompiler;

    public function _init(): void
    {
        parent::_init();
        if (!isset($this->AriaCompiler)) {
            $this->AriaCompiler = new AriaCompiler();
            //we set the default theme
            $this->setTheme('');
        }

    }


    public function file($path, $data = [], $mergeData = [])
    {
        // TODO: Implement file() method.
    }

    public function setTheme($theme = '')
    {
        $config = new Config();
        if (isset($config->app_theme)) {
            $theme_dir = Start::$SERVERROOT . '/themes/' . $config->app_theme;
        } else {

            $theme_dir = Start::$SERVERROOT . '/themes/default';

        }
        $this->AriaCompiler->properties[BaseTemplateCompiler::THEMES_DIR] = $theme_dir;
    }

    public function setThemeDir($dir)
    {
        $this->AriaCompiler->properties[BaseTemplateCompiler::THEMES_DIR] = $dir;
    }

    public function getTemplateEngineName()
    {
        return 'Aria Template Engine';
    }

    public function getTemplateEngineVersion()
    {
        return '1.0';
    }

    public function render($view, $cache = true, $replaceCache = false, $callback = null)
    {

        //disables cache, some pages dont need it, default is cache
        if(!$cache){
            $this->AriaCompiler->properties[BaseTemplateCompiler::ENABLE_CONTENT_CACHE] = false;
        }
        $this->AriaCompiler->loadView($view);
        $content = $this->AriaCompiler->render($replaceCache);
        $this->AriaCompiler->runCallback($callback);
        return $content;
    }


    /**
     * @return AriaCompiler
     */
    public function getCompiler(): AriaCompiler
    {
        return $this->AriaCompiler;
    }

    public function getLatteEngine() : Engine{
        return $this->getCompiler()->getLatte();
    }

    /**
     * Magic method alias for self::assign
     * @param <type> $k
     * @param <type> $v
     */
    public function __set($key, $value)
    {

        $this->AriaCompiler->properties[BaseTemplateCompiler::ASSIGNED][$key] = $value;

    }

    public function __get($key)
    {
       // return $this->AriaCompiler->properties[BaseTemplateCompiler::ASSIGNED][$key];
    }

    public function __isset($key)
    {

    }


    public function __call($name, $args)
    {
        $n = explode('_', $name);
        if ($n[0] === 'ob') {
            $this->AriaCompiler->properties[BaseTemplateCompiler::BLOCKS][$n[1]] = $args[0];
        }
        if ($this->AriaCompiler->properties[BaseTemplateCompiler::BASE] !== null) {
            return '';
        }
        return empty($this->AriaCompiler->properties['blocksOverride'][$n[1]]) ? $args[0] : $this->AriaCompiler->properties['blocksOverride'][$n[1]];
    }

}