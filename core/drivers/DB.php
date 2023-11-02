<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Core\Drivers;


use Core\Joi\Start;
use Core\Joi\System\Utils;
use Exception;
use SleekDB\Store;
use Tracy\Debugger;

class DB
{

    public const IS_CONFIGURED = 'Aria_studio_DB_CONGIFURED';
    public const DB_SOFTWARE = 'DB_CONNECTION';
    public const DB_HOST = 'DB_HOST';
    public const DB_USER = 'DB_USERNAME';
    public const DB_PORT = 'DB_PORT';
    public const DB_DATABASE = 'DB_DATABASE';
    public const DB_PASSWORD = 'DB_PASSWORD';
    private static $self_database_home;
    private static Store $self_database;
    private static $self_database_error_store;  // a local mysql/ oracle... db driver
    private $server_home;
    private $server; // a locally noSql database
    private $database_engine;
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
        self::$self_database_home = $server_home . '/core/store/database';
        //start the local no sql db

        try {
            self::$self_database = new Store('site', self::$self_database_home, [
                'auto_cache' => true,
                'timeout' => false,
            ]);
            $this->self_database_status = true;
        } catch (Exception $e) {
            $this->self_database_status = false;
        }

        try {
            if ($this->server->getCache()->load(self::DB_SOFTWARE) !== null) {
                $dns = $this->server->getConfig()->db_connection . ':host=' . $this->server->getConfig()->db_host . ';dbname=' . $this->server->getConfig()->db_database;

                $user = $this->server->getConfig()->db_username;
                $pass = $this->server->getConfig()->db_password;
                $this->database_engine = new \Nette\Database\Connection($dns, $user, $pass, [
                    'debugger' => true,
                    'lazy' => true
                ]);
            }

        } catch (\Exception $e) {
            $this->self_database_status = false;
            $server->getLog()->log($e);
        }

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
     * @param string $store_name
     * @return Store
     * @throws Exception
     */
    public static function getSelfDatabase($store_name = 'site'): Store
    {
        return new Store($store_name, self::$self_database_home, [
            "auto_cache" => true,
            "cache_lifetime" => null,
            "timeout" => false,
        ]);
    }

    /**
     * @param Store $self_database
     */
    public static function setSelfDatabase(Store $self_database): void
    {
        self::$self_database = $self_database;
    }

    public function create_store($store_name, $options = array())
    {

        $opt = $options ?? [
            'auto_cache' => true,
            'timeout' => false
        ];

        $status = true;
        try {
            self::$self_database = new Store($store_name, self::$self_database_home, $opt);

        } catch (Exception $e) {
            $status = false;
            self::$self_database_error_store = 'Aria DB: ' . $e->getMessage() . "\n at line " . $e->getLine() . "\n in file" . $e->getFile() . " \n " . $e->getCode() . "\n" . $e->getTraceAsString();
        }

        return $status;
    }

    public function select_store($store_name)
    {
        $status = true;
        try {
            self::$self_database = new Store($store_name, self::$self_database_home);

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

    public function generate_id($length = 10)
    {
        return Utils::getUUID();
    }

    // Generate unique identifier

    public function count($table, $filter = null)
    {
        $result = $this->get_one_row("SELECT COUNT(*) FROM `" . $table . "`" . $this->get_filter($filter) . ";");
        return $result[0];
    }

    public function get_one_row($query)
    {
        foreach ($this->query($query) as $row) {
            return $row;
        }
        return null;
    }

    public function query($query)
    {

           return $this->database_engine->query($query) ?? null;

    }

    // Count the elements in the table that match the filter

    public function get_filter($filter)
    {
        if ($filter === null) {
            return "";
        }
        $query = " WHERE ";
        $i = 1;
        foreach ($filter as $key => $value) {
            if (isset($value)) {
                $query .= "`" . $key . "` = '" . (string)$value . "'";
            } else {
                $query .= "`" . $key . "` IS NULL";
            }
            if ($i !== count($filter)) {
                $query .= " AND ";
            }
            $i++;
        }
        return $query;
    }

    public function first($table, $keys, $filter)
    {
        return $this->get_one_row("SELECT " . $keys . " FROM " . $table . "" . $this->get_filter($filter) . " LIMIT 1;");
    }

    public function add($table, $ary)
    {
        $keys = "";
        $values = "";
        $i = 1;
        foreach ($ary as $key => $value) {
            if ($value !== null) {
                if ($i !== 1) {
                    $keys .= ", ";
                    $values .= ", ";
                }
                $keys .= "`" . $key . "`";
                $values .= "'" . (string)$value . "'";
                $i++;
            }
        }
        $this->query("REPLACE INTO " . $table . " (" . $keys . ") VALUES (" . $values . ");");
    }

    public function delete($table, $filter)
    {
        $this->query("DELETE FROM " . $table . "" . $this->get_filter($filter) . ";");
    }

    public function create_table($name, $keys, $hasTime = false)
    {
        $query = "CREATE TABLE IF NOT EXISTS `" . $name . "` (";
        $i = 0;
        foreach ($keys as $key => $value) {

            $query .= "`" . $key . "` " . $value . ", ";

        }


            $query .= "`time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP);";


        $this->query($query);
        try{
            if($hasTime === false){
                $this->query("ALTER TABLE `".$name."` DROP COLUMN `time`;");
            }
        }Catch(Exception $e){

        }

    }

    public function update($table, $values, $filter)
    {
        $query = "UPDATE `" . $table . "` SET ";
        $i = 1;
        foreach ($values as $key => $value) {
            $query .= "`" . $key . "` = '" . $value . "'";
            if ($i !== count($values)) {
                $query .= ", ";
            }
            $i++;
        }
        $query .= $this->get_filter($filter) . ";";
        $this->query($query);
    }

}
