<?php 
namespace Core\config;

/**
 * This is a Joi system auto generated class, Built on : 2022-05-15 22:06:31
 */
class Config
{
	/** System version. */
	public static $version = '1.1.0';

	/** config.io last modified date and time */
	public $last_modified = 'January 01 1970 00:00:00.';

	/** @var int[] */
	public static $items = [1, 2, 3];


	public function __construct()
	{
	}


	/**
	 * local and current  theme url. called in themes as {$.theme_url}
	 */
	public function getThemeUrl()
	{
		return $this->app_url."/themes/".$this->app_theme;
	}
}

 ?>