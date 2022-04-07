<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 2:07
 */

namespace Core\tpl\Compilers;



use Nette\Utils\FileSystem;

abstract class BaseTemplateCompiler
{

    public const CONTEXT_AWARE = 'contextAware';
    public const CUR_CONTEXT = 'curContext';
    public const CUSTOM_DIRECTIVES = 'customDirectives';
    public const CONDITION = 'condition';
    public const GLOBALS = 'globals';
    public const ASSIGNED = 'assigned';
    public const EXTENSIONS = 'extensions';
    public const ROOT = 'root';
    public const CACHE_DIR = 'cacheDir';
    public const ENABLE_CONTENT_CACHE = 'enableContentCache';
    public const THEMES_DIR = 'themesDir';
    public const FILE = 'file';
    public const CONTENT = 'content';
    public const OUTPUT = 'output';
    public const BLOCKS = 'blocks';
    public const BASE = 'base';
    public const SOURCE = 'source';




    public $properties = [];


    /**
     * BaseTemplateCompiler constructor.
     */
    public function __construct()
    {

        $this->properties = [
            self::CONTEXT_AWARE => false,
            self::CUR_CONTEXT => null,
            self::CUSTOM_DIRECTIVES => [],
            self::CONDITION => [],
            self::GLOBALS => [],
            self::ASSIGNED => [],
            self::EXTENSIONS => [],
            self::ROOT => '',
            self::CACHE_DIR => '',
            self::ENABLE_CONTENT_CACHE => true,
            self::THEMES_DIR => '',
            self::FILE => '',
            self::CONTENT => '',
            self::SOURCE => '',
            self::OUTPUT => '',
            'orContent' => '',
            'isPhp' => false,
            self::BLOCKS => [],
            'blocksOverride' => [],
            self::BASE => '',
        ];

    }


    public function assignGlobals(){
        $this->properties[self::GLOBALS]['__func'] = null;
        $this->setContext($this->properties[self::GLOBALS]);
    }

    public function setFile($dir)
    {
        $this->setProperty(self::FILE, $dir);
    }


    public function setDir($dir)
    {
        $this->setProperty(self::BASE, $dir);
    }

    public function buildPathName($name)
    {

        if($name === ''){
            return false;
        }
        if(str_contains($name, '.aria') || str_contains($name, '.php') || str_contains($name, '.html')){
            return $name;
        }

        if(!str_contains($name, '.')){
            return $name.'.aria';
        }

        return str_replace('.','/',$name).'.aria';

    }

    public function getDir()
    {
        return $this->properties[self::THEMES_DIR];
    }

    public function exists($view): bool
    {
        if(FileSystem::isAbsolute(self::THEMES_DIR.'/'.$this->buildPathName($view))){
            return true;
        }
        return false;
    }

    public function assign($name, $value): void
    {
        $this->properties[self::ASSIGNED][$name] = $value;

    }

    public function setProperty($name, $value)
    {
        $this->properties[$name]=$value;

    }

    /**
     * Set the global environment variables for all templates
     * @param array $g
     * @return bool
     */
    public function setGlobals( $g = array()){
        if(!is_array($g)) {
            return false;
        }
        $this->properties[self::GLOBALS] = $g;
        return true;
    }

    public function getExtensions()
    {
        return $this->properties[self::EXTENSIONS];
    }

    /**
     * Assign multiple variables at once
     * This method should always receive get_defined_vars()
     * as the first argument
     * @param $vars
     */
    public function setContext($vars){
        if(!is_array($vars)) {
            return false;
        }
        foreach($vars as $k => $v){
            $this->assign($k,$v);
        }
        return true;
    }


    public function getContext(){
        return $this->properties[self::ASSIGNED];
    }

    /**
     * For internal use only for template inheritance.
     * @param $blocks
     */
    public function overrideBlocks($blocks) {
        $this->properties['blocksOverride'] = $blocks;
    }


    public function getProperty($name)
    {
        if(array_key_exists($name, $this->properties)){
            return $this->properties[$name];
        }

        throw new \Exception("Tried to access invalid property " . $name);
    }


}