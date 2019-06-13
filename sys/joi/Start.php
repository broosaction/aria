<?php
/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/12/2019
 * Time: 11:09
 */

namespace Core\joi;


use Core\config;
use Core\drivers\Cookies;
use Core\drivers\Security;
use Core\drivers\Sessions;
use Core\tpl\Aria;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;


class Start
{



    private $Cookies;
    private $Security;
    private $Sessions;
    private $Database;  // not yet implimented
    private $Config;
    private $Aria;

    private $server_home;

    private $log;

    /**
     * Start constructor.
     * @param string $server_dir
     */
    public function __construct($server_dir='')
    {
          $this->server_home = $server_dir;





            if ($this::isConfigClass()) {
                $this->Config = new config\Config();
            } else {
                //$this->log->addNotice('ReBuilding the Config Class');
                $this->buildConfig();

                $this->Config = new config\Config();
            }

        $this->log = new Logger($this->Config->app_name);
        try {

            $this->log->pushHandler(new StreamHandler($this->server_home.'/logs/server.log', Logger::WARNING));

        } catch (Exception $e) {

        }

        $this->Sessions = new Sessions();
            $this->Cookies = new Cookies();





        $this->Security = new Security();
        $this->Security::setServerHome($this->server_home);
        $this->Security::secure();
        $this->Aria = new Aria();

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
     * @return Security
     */
    public function getSecurity(): Security
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





}