<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 25 /Mar, 2020 @ 23:22
 */

namespace Core\Tools\CloudValkyrie;


class Utils
{




    /**
     *
     */
    protected static function unset_globals() {
        // if ( ini_get('register_globals') ) {
        $allow = array('_ENV' => 1, '_GET' => 1, '_POST' => 1, '_COOKIE' => 1, '_FILES' => 1, '_SERVER' => 1, '_REQUEST' => 1, 'GLOBALS' => 1);
        foreach ($GLOBALS as $key => $value) {
            if( ! isset( $allow[$key] ) ) unset( $GLOBALS[$key] );
        }
        //  }
    }

    /** functions base
     * @param $st_var
     * @return string
     */
    protected static function get_env($st_var): string
    {
        global $HTTP_SERVER_VARS;
        if (isset($_SERVER[$st_var])) {
            return strip_tags( $_SERVER[$st_var] );
        }

        if (isset($_ENV[$st_var])) {
            return strip_tags( $_ENV[$st_var] );
        }

        if (isset($HTTP_SERVER_VARS[$st_var])) {
            return strip_tags( $HTTP_SERVER_VARS[$st_var] );
        }

        if (getenv($st_var)) {
            return strip_tags( getenv($st_var) );
        }

        if(function_exists('apache_getenv') && apache_getenv($st_var, true)) {
            return strip_tags( apache_getenv($st_var, true) );
        }
        return '';
    }

     protected static function set_env($st_var, $data){
         global $HTTP_SERVER_VARS;
         if (isset($_SERVER[$st_var])) {
              $_SERVER[$st_var] = $data;
              return 1;
         }


         if (isset($_ENV[$st_var])) {
              $_ENV[$st_var] = $data;
             return 1;
         }

         if (isset($HTTP_SERVER_VARS[$st_var])) {
            $HTTP_SERVER_VARS[$st_var] = $data;
            return 1;
         }
         if(function_exists('apache_getenv') && apache_getenv($st_var, true)) {
             apache_setenv($st_var, $data, true);
             return 1;
         }
         return 0;
     }

     protected static function set_new_server($var, $data){
         $_SERVER[$var] = $data;
         return isset($_SERVER[$var]) ? true : false;
     }

    /**
     * @return string
     */
    public static function get_referer(): string
    {
        if( self::get_env('HTTP_REFERER') ) {
            return self::get_env('HTTP_REFERER');
        }
        return 'no referer';
    }

    public static function get_ip(): string
    {
        if (self::get_env('HTTP_X_FORWARDED_FOR')) {
            return self::get_env('HTTP_X_FORWARDED_FOR');
        }

        if (self::get_env('HTTP_CLIENT_IP')) {
            return self::get_env('HTTP_CLIENT_IP');
        }

        return self::get_env('REMOTE_ADDR');
    }

    public static function get_user_agent(): string
    {
        if(self::get_env('HTTP_USER_AGENT')) {
            return self::get_env('HTTP_USER_AGENT');
        }
        return 'none';
    }

    public static function get_user_country_code(){
        return $_SERVER["HTTP_CF_IPCOUNTRY"];
}

    protected static function get_query_string() {
        if( self::get_env('QUERY_STRING') ) {
            return str_replace('%09', '%20', self::get_env('QUERY_STRING'));
        }
        return '';
    }

    protected static function set_query_string($data) {
        if( self::get_env('QUERY_STRING') ) {
            return self::set_env('QUERY_STRING',str_replace('%20', '%09', $data));
        }
        return '';
    }

    public static function get_request_method(): string
    {
        if(self::get_env('REQUEST_METHOD')) {
            return self::get_env('REQUEST_METHOD');
        }
        return 'none';
    }

    protected static function get_regex_union() {

        return '#\w?\s?union\s\w*?\s?(select|all|distinct|insert|update|drop|delete)#is';
    }


    protected static function purify_url($url){

        if ( $pos = strpos( $uri, '?' ) ) {
            $uri = substr( $uri, 0, $pos );
        }

        if ( $pos = strpos( $uri, '#' ) ) {
            $uri = substr( $uri, 0, $pos ); // malformed
        }
        $uri = rtrim( urldecode( $uri ), '/' );

        return  preg_replace( '/\/+/', '/', $uri );
    }

    public static function gethostbyaddr() {

        if ( @ empty( $_SESSION['PHP_FIREWALL_gethostbyaddr'] ) ) {
            return $_SESSION['PHP_FIREWALL_gethostbyaddr'] = @gethostbyaddr( self::get_ip() );
        }

        return strip_tags( $_SESSION['PHP_FIREWALL_gethostbyaddr'] );

    }

    /**
     * @param $txt
     * @return mixed|string
     */
    public static function getClean($txt){
        $txt = htmlspecialchars($txt);
        $txt = str_replace(array('select', 'update', 'insert', 'where', 'like', 'or', 'and', 'set', 'into', '\'', ';', '>', '<'), array('5ev1ect', 'upd4tee', '1dn5yert', 'w6eere', '1insk', '08r', '4nd', '5eut', '1n8t0', '', '', '', ''), $txt);
        $txt = strip_tags($txt);
        return $txt;
    }



}