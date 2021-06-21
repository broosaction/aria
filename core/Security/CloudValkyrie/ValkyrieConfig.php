<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 25 /Mar, 2020 @ 22:02
 */

namespace Core\Security\CloudValkyrie;


class ValkyrieConfig
{

    private static string $version = '0.1.3';

    public static string $charset = 'UTF-8';

    protected static bool $PROTECTION = true;

    public static bool $PROTECTION_RANGE_IP_DENY = false;

    public static bool $PROTECTION_RANGE_IP_SPAM = false;

    public static bool $PROTECTION_URL = true;

    public static bool $PROTECTION_REQUEST_SERVER = true;

    public static bool $PROTECTION_SANTY = true;

    public static bool $PROTECTION_BOTS = true;

    public static bool $PROTECTION_REQUEST_METHOD = true;

    public static bool $PROTECTION_DOS = true;

    public static bool $PROTECTION_UNION_SQL = true;

    public static bool $PROTECTION_CLICK_ATTACK = true;

    public static bool $PROTECTION_XSS_ATTACK = true;

    public static bool $PROTECTION_COOKIES = true;

    public static bool $PROTECTION_IDENTITY_THEFT = true;

    public static bool $PROTECTION_POST = false;

    public static bool $PROTECTION_GET = true;

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

    public static $INTELLIGENCE_PROCESS = true;

    public static $ACTIVE_LOG = true;

    public static $LANGUAGE = 'en';

    public static $BLOCK_SCREEN = true;



    /**
     * @return string
     */
    public static function getVersion(): string
    {
        return self::$version;
    }


}