<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 25 /Mar, 2020 @ 22:02
 */

namespace Core\Tools\CloudValkyrie;


class Config
{

    private static $version = '0.0.12';

    public static $charset = 'UTF-8';

    protected static $server_home;

    protected static $PROTECTION = true;

    public static $PROTECTION_RANGE_IP_DENY = false;

    public static $PROTECTION_RANGE_IP_SPAM = false;

    public static $PROTECTION_URL = true;

    public static $PROTECTION_REQUEST_SERVER = true;

    public static $PROTECTION_SANTY = true;

    public static $PROTECTION_BOTS = true;

    public static $PROTECTION_REQUEST_METHOD = true;

    public static $PROTECTION_DOS = true;

    public static $PROTECTION_UNION_SQL = true;

    public static $PROTECTION_CLICK_ATTACK = true;

    public static $PROTECTION_XSS_ATTACK = true;

    public static $PROTECTION_COOKIES = true;

    public static $PROTECTION_IDENTITY_THEFT = true;

    public static $PROTECTION_POST = false;

    public static $PROTECTION_GET = true;
    
    public static $PROTECTION_SERVER_OVH = true;
    
    public static $PROTECTION_SERVER_KIMSUFI = true;
    
    public static $PROTECTION_SERVER_DEDIBOX = true;
    
    public static $PROTECTION_SERVER_DIGICUBE = true;
    
    public static $PROTECTION_SERVER_OVH_BY_IP = true;
    
    public static $PROTECTION_SERVER_KIMSUFI_BY_IP = true;
    
    public static $PROTECTION_SERVER_DEDIBOX_BY_IP = true;
    
    public static $PROTECTION_SERVER_DIGICUBE_BY_IP = true;
    
    public static $PROTECTION_ANTI_MALWARE = true;

    public static $PROTECTION_ANTI_SPAM = true;

    public static $INTELLI_PROCESS = false;

    public static $ACTIVE_LOG =true;

    public static $LANGUAGE = 'en';

    public static $ATTACK_BLOCK_SCREEN = true;

    private static $remote_scan_url = '';
    private static $remote_scan_api_key = '';

    /**
     * Config constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param mixed $server_home
     */
    public static function setServerHome($server_home): void
    {
        self::$server_home = $server_home;
    }

    /**
     * @return mixed
     */
    public static function getServerHome()
    {
        return self::$server_home;
    }

    /**
     * @return string
     */
    public static function getVersion(): string
    {
        return self::$version;
    }


}