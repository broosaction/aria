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

namespace Core\Joi;


use Core\Config;
use Core\Drivers\Cache;
use Core\Drivers\Cookies;
use Core\Drivers\DB;
use Core\Drivers\Sessions;
use Core\Security\Valkyrie;
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
     * @var Sessions
     */
    private Sessions $Sessions;

    /**
     * @var DB
     */
    private DB $Database;  // not yet implemented

    /**
     * @var Config\Config
     */
    private Config\Config $Config;

    private Aria $Aria;
    /**
     * @var \Nette\Caching\Cache
     */
    private \Nette\Caching\Cache $cache;

    /**
     * @var string
     */
    public string $server_home;

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
     * @var Valkyrie
     */
    private Valkyrie $valkyrie;

    /**
     * The installation path for Aria on the server (e.g. /srv/http/aria)
     */
    public static $SERVERROOT = '';

    /**
     * Start constructor.
     * @param string $server_dir
     */
    public function __construct($server_dir = '')
    {

        //set the install directory
        $this->server_home = $server_dir;
        self::$SERVERROOT = $server_dir;
        //default cache engine
        $this->cache = (new Cache($this->server_home))->getCacheEngine();

        // Check if config is accessible
        if ($this::isConfigClass()) {
            $this->Config = new Config\Config();
        } else {
            // Create config if it does not already exist
            $this->buildConfig();

            $this->Config = new Config\Config();
        }

        self::setRequiredIniValues();

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
        $options = array('headers' => getallheaders());
        $host = $options['headers']['Host'];
        if ($host !== $this->Config->app_url) {
            if ($this->httpRequest->getUrl()->getRelativeUrl() !== null) {
                $this->Config->app_url = $host . '' . $this->httpRequest->getUrl()->getRelativeUrl();
            } else {
                $this->Config->app_url = $host;
            }
        }

        $this->log = new Logger($this->server_home . '/logs', $this->getConfig()->mail_username);

        $this->Aria = new Aria();

        $this->setSessions();

        $this->valkyrie = new Valkyrie();

        $this->Cookies = new Cookies();


        $this->Database = new DB($this->server_home, $this);

    }

    /**
     * @return bool
     */
    public static function isConfigClass()
    {

        if (class_exists(Config\Config::class)) {
            return true;
        }

        return false;

    }


    /**
     * @param bool $relead
     * @return \Nette\PhpGenerator\PhpNamespace
     */
    public function buildConfig($relead = false)
    {
        $test = ConBuilder::readENV($this->server_home);
        if ($relead) {
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

            $this->getLog()->log('SESSION start error: ' . $e, $this->getLog()::ERROR);
            //show the user a detailed error page
            //OC_Template::printExceptionErrorPage($e, 500);
            die();
        }
    }


    /**
     * @return Valkyrie
     */
    public function getValkyrie(): Valkyrie
    {
        return $this->valkyrie;
    }


    /**
     * @return Config\Config
     */
    public function getConfig(): Config\Config
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
     * @return \Core\Drivers\Nette\Caching\Cache
     */
    public function getCache(): \Nette\Caching\Cache
    {
        return $this->cache;
    }

    /**
     * @param \Core\Drivers\Nette\Caching\Cache $cache
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

    /**
     * Try to set some values to the required aria default
     */
    private static function setRequiredIniValues() {

        // ini_set('session.cookie_lifetime', '0');
        // ini_set('session.cookie_httponly', 'On');
        // ini_set('session.cookie_secure', 'On');
        @ini_set('opcache.enable', '1');
        @ini_set('opcache.enable_cli', '1');
        @ini_set('opcache.jit_buffer_size', '100M');
        @ini_set('opcache.jit', '1255');
        @ini_set('default_charset', 'UTF-8');
        @ini_set('gd.jpeg_ignore_warning', '1');
        ini_set('expose_php', 'off');
        // Don't display errors and log them
        error_reporting(E_ALL | E_STRICT);
        @ini_set('display_errors', '0');
        @ini_set('log_errors', '1');
        //try to configure php to enable big file uploads.
        //this doesn´t work always depending on the webserver and php configuration.
        //Let´s try to overwrite some defaults anyway

        //try to set the maximum execution time to 60min
        if (!str_contains(@ini_get('disable_functions'), 'set_time_limit')) {
            @set_time_limit(3600);
        }

        //try to set the session lifetime
        $sessionLifeTime = Config\Config::getSessionLifeTime();
        @ini_set('gc_maxlifetime', (string)$sessionLifeTime);

        @ini_set('max_execution_time', '3600');
        @ini_set('max_input_time', '3600');

        //try to set the maximum filesize to 10G
        @ini_set('upload_max_filesize', '10G');
        @ini_set('post_max_size', '10G');
        @ini_set('file_uploads', '50');
        @ini_set('allow_url_fopen', 'on');

        header_remove('Server');
        header('Server: Aria Framework Enterprise v0.2.1');
        header('X-Powered-By: Aria Framework');
        header('X-XSS-Protection: 1');

    }

}