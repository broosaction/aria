<?php


namespace Core\drivers;



class Cache
{

      private $server_home;

      private $Cache_engine;
    /**
     * Cache constructor.
     */
    public function __construct($server_home)
    {
        $this->server_home = $server_home;

        // the `temp` directory will be the storage
        $storage = new \Nette\Caching\Storages\FileStorage($this->server_home.'/sys/store/temp');

        $this->Cache_engine = new \Nette\Caching\Cache($storage);

    }

    /**
     * @return \Nette\Caching\Cache
     */
    public function getCacheEngine(): \Nette\Caching\Cache
    {
        return $this->Cache_engine;
    }

    /**
     * @param \Nette\Caching\Cache $Cache_engine
     */
    public function setCacheEngine(\Nette\Caching\Cache $Cache_engine): void
    {
        $this->Cache_engine = $Cache_engine;
    }




}