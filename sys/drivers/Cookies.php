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

namespace Core\drivers;


use Guzzle\Plugin\Cookie\Cookie;

class Cookies
{

    private static $cookie;

    /**
     * Cookies constructor.
     */
    public function __construct()
    {


    }

    /**
     * @param $name
     * @return bool
     */
    public static function exists($name)
    {
        return isset($_COOKIE[$name]) ? true : false ;
    }


    /**
     * @param $name
     * @return mixed
     */
    public static function get($name)
    {
        return $_COOKIE[$name];
    }


    /**
     * @param $name
     * @param $value
     * @param $expiry
     * @return bool
     */
    public static function set($name, $value, $expiry)
    {

        if(setcookie($name , $value, time() + $expiry, '/')){
            return true;
        }


        return false;
    }


    /**
     * @param $name
     */
    public static function delete ($name)
    {
        self::set($name, '', time() - 1 );
        self::un_set($name);
    }

    public static function un_set($key){
        unset($_COOKIE[$key]);
    }

}