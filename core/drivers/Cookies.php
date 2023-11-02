<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/2/2019
 * Time: 12:37
 */

namespace Core\Drivers;


use Core\Joi\System\Exceptions\InvalidArgumentException;
use ErrorException;
use Guzzle\Plugin\Cookie\Cookie;

class Cookies
{

    private static $cookie;

    /**
     * Cookies constructor.
     */
    public function __construct()
    {
        $cookie = $_COOKIE;
    }

    /**
     * checks if a cookie exists
     * @param $name
     * @return bool
     */
    public static function exists($name): bool
    {
        return isset($_COOKIE[$name]);
    }


    /**
     * easy way to get a cookie
     * @param $name
     * @return mixed
     */
    public static function get($name)
    {
        return $_COOKIE[$name] ?? null;
    }


    /**
     * sets a cookie
     * @param $name
     * @param $value
     * @param $expiry
     * @return bool
     */
    public static function set($name, $value, $expiry): bool
    {
        if(aria()->getHttpResponse()->isSent() === false) {
            if (setcookie($name, $value, time() + $expiry, '/', true)) {
                return true;
            }
            return false;
        }

        return false;
    }


    /**
     * easy way to delete a cookie
     * @param $name
     */
    public static function delete ($name): void
    {
        self::set($name, '', time() - 1 );
        self::un_set($name);
    }

    /**
     * makes a cookie to no-longer exists
     * @param $key
     */
    public static function un_set($key): void
    {
        unset($_COOKIE[$key]);
    }

    /**
     * returns the users mode either night or day depends with the users machine theme
     * works well on edge browser windows 10
     * @return mixed|null
     */
    public static function getDisplayMode(){
        return self::get('mode');
    }


    public static function getTelemetryDeviceId(){
        return self::get('MicrosoftApplicationsTelemetryDeviceId');
    }

    /**
     * returns the Microsoft edge browser time when the user first opened our site
     * @return mixed|null
     */
    public static function getTelemetryFirstLaunchTime(){
        $dd = self::get('MicrosoftApplicationsTelemetryFirstLaunchTime');
        if($dd !== null){
            return explode('T', $dd)[1];
        }
        return null;
    }

    /**
     * returns the Microsoft edge browser date when the user first opened our site
     * @return mixed|null
     */
    public static function getTelemetryFirstLaunchData(){
        $dd = self::get('MicrosoftApplicationsTelemetryFirstLaunchTime');
        if($dd !== null){
          return explode('T', $dd)[0];
        }
        return null;
    }

}