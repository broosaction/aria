<?php

namespace Core\Drivers\ORM\Database;

use Core\Drivers\ORM\Database\Connections\{MySQLConnection,PostgresSQLConnection,SQLServerConnection,SQLiteConnection};
use PDO;
class Connector{

	private $connections,$pdos=[];
	private static $pdo,$instance=NULL;

	private function checkConnection($driver){
        switch ($driver) {
            case 'mysql':
                return new MySQLConnection;
                break;

            case 'pgsql':
                return new PostgresSQLConnection;
                break;

            case 'sqlsrv':
                return new SQLServerConnection;
                break;

            case 'sqlite':
                return new SQLiteConnection;
                break;

            default:
                throw new \Exception("Your database driver is not supported", 1);
                break;
        }
	}

	public function getPDO(array $config){
		if(!isset($config['driver'])){
			throw new \Exception("You need to add database driver", 1);
		}
		$connection=NULL;
		$availableDrivers=PDO::getAvailableDrivers();

		$this->checkDriver($config['driver'],$availableDrivers);

		$connection=$this->checkConnection($config['driver']);

		return $connection->getConnection($config);

		throw new \Exception("Your request database driver is not supported", 1);
	}

	private function boot(){
		if(empty($this->connections)){
			$this->connections['mysql']=[
				'driver' => 'mysql'
			];
			$this->connections['pgsql']=[
				'driver' => 'pgsql'
			];
			$this->connections['sqlsrv']=[
				'driver' => 'sqlsrv'
			];
		}
	}

	public function addConnection(string $connection){
		$this->boot();
		if(!isset($this->connections[$connection])){
			$this->connections[$connection]=NULL;
		}
		return $this;
	}

	public function createConnection(string $connection,array $config){
		$this->boot();
		if(array_key_exists($connection, $this->connections)){
			$resultConnection=isset($this->connections[$connection]['driver']) ? $this->connections[$connection]+$config : $config;
			$this->pdos[$connection]=$this->getPDO($resultConnection);
			self::$instance=$this;
		}else{
			throw new \Exception("You are connecting to unavialble database connection", 1);
		}
		
	}

	public function selectConnection(string $connection){
		if(isset($this->pdos[$connection])){
			self::$pdo=$this->pdos[$connection];
		}else{
			throw new \Exception("Your database connection is unavailable", 1);
		}
		
	}

	public static function getConnection(){ return self::$pdo; }

	public static function getInstance(){ return self::$instance; }

	public function executeConnect(string $connection){
		if(isset($this->pdos[$connection])){
			return $this->pdos[$connection];
		}
		throw new \Exception("Your database connection is unavailable", 1);
	}

	private function checkDriver($driver,$availableDrivers){
		if(!in_array($driver, $availableDrivers)){
			throw new \Exception("You need to install ".$driver." driver", 1);
		}
	}
}