<?php

namespace Core\Drivers\ORM\Cache;

use Memcached;
class MemcachedCache extends CacheAbstract{

	protected $cachedObject;
	
	public function __construct(Memcached $memcached){
		$this->cachedObject=$memcached;
	}


}