<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/12/2019
 * Time: 11:09
 */

namespace Core\joi;


use Core\config;
use Core\drivers\Cache;
use Core\drivers\Cookies;
use Core\drivers\DB;
use Core\drivers\Security;
use Core\drivers\Sessions;
use Core\Tools\CloudValkyrie\CloudValkyrie;
use Core\tpl\Aria;
use Exception;
use Tracy\Logger;


class Start
{



    private $Cookies;
    private $Security;
    private $Sessions;
    private $Database;  // not yet implimented
    private $Config;
    private $Aria;
    private $cache;

    private $server_home;

    private $log;

    /**
     * Start constructor.
     * @param string $server_dir
     */
    public function __construct($server_dir='')
    {
          $this->server_home = $server_dir;
        $this->cache = (new Cache($this->server_home))->getCacheEngine();

        $options = array('headers' => getallheaders());


        $this->cache->save('test', 'System Up-Time');

            if ($this::isConfigClass()) {
                $this->Config = new config\Config();
            } else {
                //$this->log->addNotice('ReBuilding the Config Class');
                $this->buildConfig();

                $this->Config = new config\Config();
            }

        $host = $options['headers']['Host'];
            if($host !== $this->Config->app_url){
                $this->Config->app_url = $host.'/cloud';
            }

        $this->log = new Logger($this->server_home.'/logs',$this->getConfig()->mail_username);

        $this->Security = new CloudValkyrie(); // security is set first.
        \Core\Tools\CloudValkyrie\Config::setServerHome($this->server_home);
        $this->Security::secure();
        $this->Sessions = new Sessions();
        $this->Cookies = new Cookies();

       // $this->Security::setServerHome($this->server_home);

        $this->Aria = new Aria($this);

        $this->Database = new DB($this->server_home, $this);

    }

    /**
     * @return bool
     */
    public static function isConfigClass()
    {

        if (class_exists(config\Config::class)) {
           return true;
        }

        return false;

    }


    /**
     * @param bool $relead
     * @return \Nette\PhpGenerator\PhpNamespace
     */
    public function buildConfig($relead=false)
    {

        $test = ConBuilder::readENV($this->server_home);
        if($relead) {
             header('location:./');
        }
        return $test;
    }

    /**
     * @return string
     */
    public function getServerHome(): string
    {
        return $this->server_home;
    }

    /**
     * @param string $server_home
     */
    public function setServerHome(string $server_home)
    {
        $this->server_home = $server_home;
    }

    /**
     * @return Cookies
     */
    public function getCookies(): Cookies
    {
        return $this->Cookies;
    }

    /**
     * @param Cookies $Cookies
     */
    public function setCookies(Cookies $Cookies)
    {
        $this->Cookies = $Cookies;
    }

    /**
     * @return Sessions
     */
    public function getSessions(): Sessions
    {
        return $this->Sessions;
    }

    /**
     * @param Sessions $Sessions
     */
    public function setSessions(Sessions $Sessions)
    {
        $this->Sessions = $Sessions;
    }

    /**
     * @return CloudValkyrie
     */
    public function getSecurity(): CloudValkyrie
    {
        return $this->Security;
    }


    /**
     * @return config\Config
     */
    public function getConfig(): config\Config
    {
        return $this->Config;
    }

    /**
     * @return Logger
     */
    public function getLog(): Logger
    {
        return $this->log;
    }

    /**
     * @param Logger $log
     */
    public function setLog(Logger $log)
    {
        $this->log = $log;
    }

    /**
     * @return Aria
     */
    public function getAria(): Aria
    {
        return $this->Aria;
    }

    /**
     * @param Aria $Aria
     */
    public function setAria(Aria $Aria)
    {
        $this->Aria = $Aria;
    }

    /**
     * @return \Core\drivers\Nette\Caching\Cache
     */
    public function getCache(): \Nette\Caching\Cache
    {
        return $this->cache;
    }

    /**
     * @param \Core\drivers\Nette\Caching\Cache $cache
     */
    public function setCache(\Nette\Caching\Cache $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @return DB
     */
    public function getDatabase(): DB
    {
        return $this->Database;
    }

    /**
     * @param DB $Database
     */
    public function setDatabase(DB $Database): void
    {
        $this->Database = $Database;
    }





}