<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Core\Drivers;



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
        $storage = new \Nette\Caching\Storages\FileStorage($this->server_home.'/core/store/temp');

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
