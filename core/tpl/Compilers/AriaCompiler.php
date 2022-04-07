<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 2:13
 */

namespace Core\tpl\Compilers;


use Core\joi\Start;
use Core\tpl\Contracts\TemplateCompiler;

use Core\tpl\Handlers\Aria\BlocksHandler;
use Core\tpl\Handlers\Aria\ExtendsHandler;
use Core\tpl\Handlers\Aria\HandleLoops;
use Core\tpl\Handlers\Aria\HandleVar;
use Core\tpl\Handlers\Aria\IfHandler;
use Core\tpl\Handlers\Aria\IncludeHandler;
use Core\tpl\Handlers\Aria\InitExtentions;
use Core\tpl\Handlers\Aria\VarHandler;
use Core\tpl\Support\Utils;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;


class AriaCompiler extends BaseTemplateCompiler implements TemplateCompiler
{

    private FileStorage $cacheStorage;

    /**
     * Load the desired template
     * @param null $file
     * @return bool
     */
    public function loadView($file = NULL)
    {
        if ($file !== NULL) {
            $this->setFile($this->buildPathName($file));
        }
        if (empty($this->properties[self::FILE])) {
            return false;
        }
        $ext = explode('.', $file);
        $ext = $ext[count($ext) - 1];
        if ($ext === "php") {
            $this->properties['isPhp'] = true;
        } else {
            if (!file_exists($this->properties[self::THEMES_DIR] . '/' . $this->properties[self::FILE])) {
                echo "<span style=\"display: inline-block; background: red; color: white; padding: 2px 8px; border-radius: 10px; font-family: 'Lucida Console', Monaco, monospace, sans-serif; font-size: 80%\"><b>Aria</b>: unable to load file '" . $this->properties[self::THEMES_DIR] . '/' . $this->properties[self::FILE] . "'</span>";
                return false;
            }

            $this->properties[self::SOURCE] =  FileSystem::read($this->properties[self::THEMES_DIR] . '/' . $this->properties[self::FILE]);
            $this->properties[self::CONTENT] =& $this->properties[self::SOURCE];

        }
        $this->properties['orContent'] = $this->properties[self::CONTENT];
        return true;
    }

    public function render($replaceCache = false)
    {
        $content = $this->getProperty(self::CONTENT);
        if ($replaceCache) {
            $hash = sha1($this->properties['orContent']);
            $this->server->getCache()->remove($hash);
        }
        //set our cache storage
        $this->cacheStorage = new FileStorage(Start::$SERVERROOT.'/core/store/cache');
        //globals are needed either via cached or live
        $this->assignGlobals();
        //extensions are needed too
        (new InitExtentions())->handle($content, $this);
        if ($this->getFromCache() === false) {
            (new ExtendsHandler())->handle($content, $this);
            (new BlocksHandler())->handleBlockMacros($content);
            (new BlocksHandler())->handle($content, $this);
            (new IncludeHandler())->handle($content, $this);
            (new IfHandler())->handleIfMacros($content);
            (new HandleLoops())->handleLoopMacros($content);
            (new HandleLoops())->handle($content, $this);
            (new IfHandler())->handle($content, $this);
            (new HandleVar())->handle($content, $this);
            (new VarHandler())->handle($content, $this);
            $this->properties[self::CONTENT] = $content;
            $this->make();
        }

        if ($this->getProperty(self::BASE) !== '') {
            // This template has inheritance
            $parent = new self($this->server);
            $parent->setContext($this->getProperty(self::GLOBALS));
            $parent->overrideBlocks($this->getProperty(self::BLOCKS));
            return $this->render();
        }
        return $this->getProperty(self::OUTPUT);
    }

    public function getFromCache()
    {
        //the cache key, well the will use the content hash in-case content change
        $hash = sha1($this->properties['orContent']);
        //if cache is not enabled we leave from here
        if ($this->properties[self::ENABLE_CONTENT_CACHE] !== true) {
            return false;
        }

        foreach ($this->properties[self::ASSIGNED] as $var => $val) {
            ${$var} = $val;
        }
        $_cache = new Cache($this->cacheStorage);
        //we load our sourcecode from cache
        $this->properties[self::SOURCE] = $_cache->load($hash);

        if($this->properties[self::SOURCE] === null){
           return false;
        }

        $this->properties[self::SOURCE] = "<!-- Aria Framework Cached -->\n".$this->properties[self::SOURCE];
        ob_start();
        $e = eval('?>' . $this->properties[self::SOURCE]);
        $this->properties[self::OUTPUT] = ob_get_clean();
        if ($e === false) {
            die("Error: unable to compile template");
        }

        return true;
    }

    public function make($data = [], $mergeData = [])
    {
        foreach ($this->properties[self::ASSIGNED] as $var => $val) {
            ${$var} = $val;
        }
        if ($this->properties[self::ENABLE_CONTENT_CACHE] === true) {
            $this->saveCache();
        }

        ob_start();
        $e = eval('?>' . $this->properties[self::CONTENT]);
        $this->properties[self::OUTPUT] = ob_get_clean();
        if ($e === false) {
            die("Error: unable to compile template");
        }
    }

    public function saveCache()
    {
        //the cache engine
        $cache = new Cache($this->cacheStorage);
        //our hash
        $hash =  sha1($this->properties['orContent']);
        //the chaching
        $cache->save($hash, Utils::minifyHTML($this->properties[self::CONTENT]), array(
            Cache::EXPIRE => '12 hours', // needs a longer time time while in production
            Cache::SLIDING => false,
            Cache::FILES => $this->properties[self::THEMES_DIR] . '/' . $this->properties[self::FILE],
        ));
    }

    public function composer($name, $callback)
    {
        if (!empty($this->properties[self::EXTENSIONS][$name])) {
            return false;
        }
        if (!is_callable($callback)) {
            return false;
        }
        $this->properties[self::EXTENSIONS][$name] = $callback;
        return true;
    }

    public function compose()
    {
        $args = func_get_args();
        if (empty($args[0])) {
            return "[empty Extension]";
        }
        if (empty($this->properties[self::EXTENSIONS][$args[0]])) {
            return "[invalid Extension '$args[0]']";
        }
        try {
            $ret = call_user_func_array($this->properties[self::EXTENSIONS][$args[0]], array_slice($args, 1));
        } catch (\Exception $e) {
            throw new \Exception("<span style=\"display: inline-block; background: red; color: white; padding: 2px 8px; border-radius: 10px; font-family: 'Lucida Console', Monaco, monospace, sans-serif; font-size: 80%\"><b>$args[0]</b>: " . $e->getMessage() . "</span>");
        }
        return $ret;

    }


    public function runCallback($callback)
    {
        // TODO: Implement runCallback() method.
    }

    public function __isset($key)
    {
        // TODO: Implement __isset() method.
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function __get($key)
    {
        return $this->getProperty($key);
    }

    public function __set($key, $value)
    {
        $this->assign($key, $value);
    }

    public function __call($name, $args)
    {
        $n = explode('_', $name);
        if ($n[0] === 'ob') {
            $this->properties[self::BLOCKS][$n[1]] = $args[0];
        }
        if ($this->properties[self::BASE] !== null) {
            return '';
        }
        return empty($this->properties['blocksOverride'][$n[1]]) ? $args[0] : $this->properties['blocksOverride'][$n[1]];
    }

}