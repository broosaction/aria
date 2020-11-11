<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Core\drivers;


use Core\joi\Start;
use Exception;
use SleekDB\SleekDB;

class DB
{

    private $server_home;

    private $server;
    private $database_engine;  // a local mysql/ oracle... db driver

    private static $self_database_home;
    private static $self_database; // a locally noSql database
    private static $self_database_error_store;


    private $self_database_status = true;
    private $database_status = true;


    /**
     * Cache constructor.
     * @param $server_home
     * @param Start $server
     */
    public function __construct($server_home, Start $server)
    {
        $this->server_home = $server_home;

        $this->server = $server;

        $dns = $this->server->getConfig()->db_connection.':host='.$this->server->getConfig()->db_host.';dbname='.$this->server->getConfig()->db_database;

        $user = $this->server->getConfig()->db_username;
        $pass = $this->server->getConfig()->db_password;

        self::$self_database_home = $server_home.'/sys/store/database';
          //start the local no sql db


        try {
            self::$self_database = SleekDB::store('site', self::$self_database_home, [
                'auto_cache' => true,
                'timeout' => 120
            ]);
            $this->self_database_status = true;
        } catch (Exception $e) {
           $this->self_database_status = false;
        }

      //  try {

            $this->database_engine = new \Nette\Database\Connection($dns, $user, $pass);

     //   }catch (\Exception $e){
       //     $this->self_database_status = false;
     //   }



        // the `temp` directory will be the storage

    }


    public function create_store($store_name, $options = '')
    {

        if(isset($options)){
            $opt = $options;
        }else{
            $opt =  [
                'auto_cache' => true,
                'timeout' => 120
            ];
        }

        $status = true;
        try {
            self::$self_database = SleekDB::store($store_name, self::$self_database_home, $opt);

        } catch (Exception $e) {
            $status = false;
          self::$self_database_error_store = 'Aria DB: '.$e->getMessage() . "\n at line " . $e->getLine() . "\n in file".$e->getFile()." \n " . $e->getCode() . "\n".$e->getTraceAsString();
        }

        return $status;
    }

    public function select_store($store_name){
        $status = true;
        try {
            self::$self_database = SleekDB::store($store_name, self::$self_database_home);

        } catch (Exception $e) {
            $status = false;

        }

        return $status;
    }

    /**
     * @return bool
     */
    public function isSelfDatabaseStatus(): bool
    {
        return $this->self_database_status;
    }

    /**
     * @return bool
     */
    public function isDatabaseStatus(): bool
    {
        return $this->database_status;
    }

    /**
     * @return string
     */
    public static function getSelfDatabaseHome(): string
    {
        return self::$self_database_home;
    }

    /**
     * @param string $self_database_home
     */
    public static function setSelfDatabaseHome(string $self_database_home): void
    {
        self::$self_database_home = $self_database_home;
    }

    /**
     * @return \Nette\Database\Connection
     */
    public function getDatabaseEngine(): \Nette\Database\Connection
    {
        return $this->database_engine;
    }

    /**
     * @param \Nette\Database\Connection $database_engine
     */
    public function setDatabaseEngine(\Nette\Database\Connection $database_engine): void
    {
        $this->database_engine = $database_engine;
    }

    /**
     * @return SleekDB
     */
    public static function getSelfDatabase(): SleekDB
    {
        return self::$self_database;
    }

    /**
     * @param SleekDB $self_database
     */
    public static function setSelfDatabase(SleekDB $self_database): void
    {
        self::$self_database = $self_database;
    }









}