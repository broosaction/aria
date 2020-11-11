<?php 
namespace App\Bootstrap;

use Core\joi\Start;
use Core\router\Web;

/**
 * This is a Joi System auto generated class, Built on : 2020-11-11 14:10:46
 */
class Boot
{
	/** Themes directory. */
	private $theme_dir = '';

	/** Themes directory home. */
	private $theme_home = '';

	/** System server */
	private Start $sys;


	/**
	 * @return null
	 */
	public function __construct(Start $server)
	{
		if(isset($server)){
		            $this->sys =  $server;
		        }else{
		            $this->sys = new Start(__DIR__);
		        }
		        if(isset($this->sys->getConfig()->app_theme)) {
		            $this->theme_dir = $this->sys->getServerHome() . '/themes/' . $this->sys->getConfig()->app_theme;

		            $this->theme_home = $this->sys->getConfig()->app_url . '/themes/' . $this->sys->getConfig()->app_theme;
		        }else{

		            $this->theme_dir = $this->sys->getServerHome() . '/themes/default';

		            $this->theme_home = $this->sys->getConfig()->app_url . '/themes/default';
		        }


		        $router = new Web();

		        $tpl = $this->sys->getAria()->getTonic();

		        $tpl->set_themes_dir($this->theme_dir);

		        $cache = (new  \Core\drivers\Cache($server->getServerHome()))->getCacheEngine();

		        $tpl::setGlobals();

		        if(!$this->sys->getConfig()->app_cache) {

		            $tpl::$cache_dir = $this->theme_dir . '/cache/';

		            $tpl::$enable_content_cache = true;
		        }


		new \App\Controllers\HttpStatus($router,$server,$tpl);
		new \App\Controllers\Index($router,$server,$tpl);

		$router->run();
	}
}

 