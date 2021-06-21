<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 14:58
 */

namespace Core\Security\Utils;


use Core\joi\System\Utils;

class ValkyrieUtils
{

    /**
     *
     */
    public static function unset_globals()
    {

        $allow = array('_ENV' => 1, '_GET' => 1, '_POST' => 1, '_COOKIE' => 1, '_FILES' => 1, '_SERVER' => 1, '_REQUEST' => 1, 'GLOBALS' => 1);
        foreach ($GLOBALS as $key => $value) {
            if (!isset($allow[$key])) {
                unset($GLOBALS[$key]);
            }
        }

    }


    public static function set_new_server($var, $data)
    {
        $_SERVER[$var] = $data;
        return isset($_SERVER[$var]);
    }

    /**
     * @return string
     */
    public static function get_referer(): string
    {
        if (Utils::get_env('HTTP_REFERER')) {
            return Utils::get_env('HTTP_REFERER');
        }
        return 'no referer';
    }

    public static function get_user_agent(): string
    {
        if (Utils::get_env('HTTP_USER_AGENT')) {
            return Utils::get_env('HTTP_USER_AGENT');
        }
        return 'none';
    }

    public static function get_user_country_code()
    {
        return $_SERVER["HTTP_CF_IPCOUNTRY"] ?? null;
    }

    public static function get_query_string()
    {
        if (Utils::get_env('QUERY_STRING')) {
            return str_replace('%09', '%20', Utils::get_env('QUERY_STRING'));
        }
        return '';
    }

    public static function set_query_string($data)
    {
        if (Utils::get_env('QUERY_STRING')) {
            return Utils::set_env('QUERY_STRING', str_replace('%20', '%09', $data));
        }
        return '';
    }

    public static function get_request_method(): string
    {
        if (Utils::get_env('REQUEST_METHOD')) {
            return Utils::get_env('REQUEST_METHOD');
        }
        return 'none';
    }

    public static function get_regex_union()
    {
        return '#\w?\s?union\s\w*?\s?(select|all|distinct|insert|update|drop|delete)#is';
    }

    public static function purify_url($url)
    {

        if ($pos = strpos($url, '?')) {
            $url = substr($url, 0, $pos);
        }

        if ($pos = strpos($url, '#')) {
            $url = substr($url, 0, $pos); // malformed
        }
        $url = rtrim(urldecode($url), '/');

        return preg_replace('/\/+/', '/', $url);
    }

    public static function gethostbyaddr()
    {

        if (@ empty($_SESSION['PHP_FIREWALL_gethostbyaddr'])) {
            return $_SESSION['PHP_FIREWALL_gethostbyaddr'] = @gethostbyaddr(Utils::get_ip_address());
        }

        return strip_tags($_SESSION['PHP_FIREWALL_gethostbyaddr']);

    }

    /**
     * @param $txt
     * @return mixed|string
     */
    public static function getClean($txt)
    {
        $txt = htmlspecialchars($txt);
        $txt = str_replace(array('select', 'update', 'insert', 'where', 'like', 'or', 'and', 'set', 'into', '\'', ';', '>', '<'), array('5ev1ect', 'upd4tee', '1dn5yert', 'w6eere', '1insk', '08r', '4nd', '5eut', '1n8t0', '', '', '', ''), $txt);
        $txt = strip_tags($txt);
        return $txt;
    }

}