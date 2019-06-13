<?php
/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/2/2019
 * Time: 12:36
 */

namespace Core\drivers;


class Sessions
{
  // needs a live session aggregation method

    private $driver;

    private static $valid=false;

    public function __construct($driver='')
    {

        if($driver === '' || $driver==='array') {
            if (session_start()) {

            } else {
                session_start();
            }
            self::protect();
        }

/**
        if($this->driver ==='file'){
            // file driver
        }

        if($this->driver === 'object'){

            // object..
        }

*/
    }

    /**
     *
     */
    public static function protect()
    {

        $ip = self::getIP();
        $user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";

        if(self::exists('config.ip')){
            if($ip === self::get("config.ip") && $user_agent === self::get("config.user_agent")){
                self::$valid = true;
            }else{
                self::$valid = false;
                //$this->setError(1, "Hacking attempt !!!");
               $block = new Security();
                self::destroy();
               $block::blockSession();

            }
        }else{
            self::put("config.ip", $ip);
            self::put("config.user_agent",$user_agent);
            self::$valid = true;
        }


    }

    /**
     * @return bool
     */
    public static function isValid()
    {
        return self::$valid;
    }

    /**
     * @param $name
     * @return bool
     */
    public static function exists($name)
    {
        return isset($_SESSION[$name]) ? true : false;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function put($key, $value){
        return $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     * @return null
     */
    public static function get($key)
    {
        if(self::exists($key)){
            return $_SESSION[$key];
        }
        return null;
    }

    /**
     * @param $key
     */
    public static function delete($key)
    {
        if(self::exists($key)){
            unset($_SESSION[$key]);
        }
    }

    /**
     * @param $key
     * @param string $string
     * @return null
     */
    public static function flash($key, $string ='')
    {
        if(self::exists($key)){
            $session = self::get($key);
            self::delete($key);
            return $session;
        }

        self::put($key, $string);
        return null;
    }

    /**
     *
     */
    public static function destroy()
    {

        unset($_SESSION);
        session_unset();
        session_destroy();
    }

    /**
     * @param bool $trustProxy
     * @return mixed
     */
    public static function getIP($trustProxy = false)
    {
        if (!$trustProxy) {
            return $_SERVER['REMOTE_ADDR'];
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ipAddress = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        return $ipAddress;
    }

}