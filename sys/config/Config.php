<?php 
namespace Core\config;

/**
 * This is a Joi system auto generated class, Built on : 2020-11-11 14:10:38
 */
class Config
{
	/** System version. */
	public static $version = '1.1.0';

	/** config.io last modified date and time */
	public $last_modified = 'November 10 2020 18:02:11.';

	/** app_name */
	public $app_name = 'aria';

	/** app_env */
	public $app_env = 'local';

	/** app_key */
	public $app_key = '';

	/** app_debug */
	public $app_debug = 'true';

	/** app_url */
	public $app_url = 'http://brooshost:8081/frame';

	/** app_theme */
	public $app_theme = 'default';

	/** app_type */
	public $app_type = 'native';

	/** app_cache */
	public $app_cache = 'true';

	/** db_connection */
	public $db_connection = 'mysql';

	/** db_host */
	public $db_host = '127.0.0.1';

	/** db_port */
	public $db_port = '3306';

	/** db_database */
	public $db_database = '';

	/** db_username */
	public $db_username = 'root';

	/** db_password */
	public $db_password = '';

	/** mail_driver */
	public $mail_driver = 'smtp';

	/** mail_host */
	public $mail_host = 'smtp.mailtrap.io';

	/** mail_port */
	public $mail_port = '2525';

	/** mail_username */
	public $mail_username = 'null';

	/** mail_password */
	public $mail_password = 'null';

	/** mail_encryption */
	public $mail_encryption = 'null';

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