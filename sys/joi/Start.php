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
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Tracy\Logger;


class Start
{


    /**
     * @var Cookies
     */
    private Cookies $Cookies;

    /**
     * @var CloudValkyrie
     */
    private CloudValkyrie $Security;

    /**
     * @var Sessions
     */
    private Sessions $Sessions;

    /**
     * @var DB
     */
    private DB $Database;  // not yet implemented

    /**
     * @var config\Config
     */
    private config\Config $Config;

    private Aria $Aria;
    /**
     * @var \Nette\Caching\Cache
     */
    private \Nette\Caching\Cache $cache;

    /**
     * @var string
     */
    protected string $server_home;

    /**
     * @var Logger
     */
    private Logger $log;

    /**
     * check if Aria runs in cli mode
     */
    public static $CLI = false;


    /**
     * @var boolean
     */
    public static $isAJAX = false;


    /**
     * @var boolean
     */
    public static $isHTTPS = false;
    /**
     * @var Request
     */
    private Request $httpRequest;
    /**
     * @var Response
     */
    private Response $httpResponse;


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

             // Check if config is accessible
            if ($this::isConfigClass()) {
                $this->Config = new config\Config();
            } else {
                // Create config if it does not already exist
                $this->buildConfig();

                $this->Config = new config\Config();
            }

        // Override php.ini and log everything if we're troubleshooting
        if ($this->Config->app_env === 'local') {
            error_reporting(E_ALL);
        }


        if (!date_default_timezone_set('UTC')) {
            throw new \RuntimeException('Could not set timezone to UTC');
        }

        $this->httpRequest = new Request(new UrlScript($this->Config->app_url));
            self::$isAJAX = $this->httpRequest->isAjax();
            self::$isHTTPS = $this->httpRequest->isSecured();

            $this->httpResponse = new Response();

            /*
             * this was needed to fix some bug
             */
        $host = $options['headers']['Host'];
            if($host !== $this->Config->app_url){
                if($this->httpRequest->getUrl()->getRelativeUrl() !== null){
                    $this->Config->app_url = $host.'/'.$this->httpRequest->getUrl()->getRelativeUrl();
                }else{
                    $this->Config->app_url = $host;
                }
            }
            //@todo
        // Resolve /login to /login/ to ensure to always have a trailing
        // slash which is required by URL generation.

        $this->log = new Logger($this->server_home.'/logs',$this->getConfig()->mail_username);
        $this->setSessions();
        \Core\Tools\CloudValkyrie\Config::setServerHome($this->server_home);
        $this->Security = new CloudValkyrie(); // security is set first.
        $this->Security::secure();
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
    public function setSessions(Sessions $Sessions = null)
    {
        try {
            if (self::$isHTTPS) {
                ini_set('session.cookie_secure', true);
            }

            // prevents javascript from accessing php session cookies
            ini_set('session.cookie_httponly', 'true');


            $this->Sessions = $Sessions ?? new Sessions();
        } catch (Exception $e) {

            $this->getLog()->log('SESSION start error: '.$e,$this->getLog()::ERROR);
            //show the user a detailed error page
            //OC_Template::printExceptionErrorPage($e, 500);
            die();
        }
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

    /**
     * @return Request
     */
    public function getHttpRequest(): Request
    {
        return $this->httpRequest;
    }

    /**
     * @return Response
     */
    public function getHttpResponse(): Response
    {
        return $this->httpResponse;
    }





}