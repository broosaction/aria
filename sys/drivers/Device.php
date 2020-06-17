<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/11/2019
 * Time: 22:12
 */

namespace Core\drivers;


use Core\Tools\ipinfo\IPinfo;
use Exception;
use Session;


class Device
{
//a5107b6fbebe9e

    private static $ipInfo;  //object

    private static $ipAddress;

    private static $hostname;


    private static $country;

    private static $country_code;

    private static $city;

    private static $source;

     private static $error;

     private static $latitude;

     private static $longitude;

     private static $loc;

     private static $phone;

     private static $postal;

     private static $region;

     private static $asn;

     private static $providerDomain;

     private static $providerName;

     private static $providerType;

     private static $providerRouter;

     private static $companyDomain;

     private static $companyName;

     private static $companyType;

    private static $ipUrl;

    public static function systemInfo()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform    = 'Regular';
        $os_array       = array('/windows phone 10/i'    =>  'Windows Phone 10',
            '/windows phone 8/i'    =>  'Windows Phone 8',
            '/windows phone os 7/i' =>  'Windows Phone 7',
            '/windows nt 10/i'     =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/CrOS/i'         =>  'chromium OS',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS',
            '/linux/i'              =>  'Linux',
            '/Unix/i'              =>  'Unix',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iPhone/i'   =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/Android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile');


        foreach ($os_array as $regex => $value) {

            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
            }

        }

        return $os_platform;

    }





    private static function getIpInfo($ip='')
    {
     $access_token = 'a5107b6fbebe9e';
        if(isset($ip)){
            self::$ipUrl = $ip;
        }else{

            self::$ipUrl = Security::get_ip();
        }
        try {
            $client = new IPinfo($access_token);

            $ip_address = '216.239.36.21';
            self::$ipInfo = $client->getDetails(self::$ipUrl);

            self::$ipAddress = self::$ipInfo->ip;
            self::$hostname = self::$ipInfo->hostname;

            self::$country_code = self::$ipInfo->country;
            self::$country = self::$ipInfo->country_name;
            self::$city = self::$ipInfo->city;
            //list(self::$latitude, self::$longitude) = explode(',', self::$ipInfo->loc);
            self::$loc = self::$ipInfo->loc;

            self::$latitude = self::$ipInfo->latitude;
            self::$longitude = self::$ipInfo->longitude;
            self::$phone = self::$ipInfo->phone;
            self::$postal = self::$ipInfo->postal;
            self::$region = self::$ipInfo->region;
            self::$source = 'ipinfo.io';

            $asn = self::$ipInfo->asn;
            self::$asn = $asn->asn;
            self::$providerDomain = $asn->domain;
            self::$providerName = $asn->name;
            self::$providerType = $asn->type;
            self::$providerRouter = $asn->route;

             $company = self::$ipInfo->company;
            self::$companyDomain = $company->domain;
            self::$companyName = $company->name;
            self::$companyType = $company->type;

            /*
            try {
              $googleLocation = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . self::$ipInfoLatitude . ',' . self::$ipInfoLongitude . '&sensor=false'));
              self::$ipInfoAddress = $googleLocation->results[2]->formatted_address;
            } catch (Exception  $e) {
              $googleLocation = null;
            }
            */
            self::$source = 'ipinfo.io';
            self::$error = false;
            return true;
        } catch (Exception  $e) {
            try {
                self::$ipInfo = json_decode(file_get_contents('http://freegeoip.net/json' . self::$ipUrl));
                self::$ipAddress = self::$ipInfo->ip;
                self::$country_code = self::$ipInfo->country_code;
                self::$latitude = self::$ipInfo->latitude;
                self::$longitude = self::$ipInfo->longitude;
                try {
                  $googleLocation = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . self::$latitude . ',' . self::$longitude . '&sensor=false'));
                  self::$postal = $googleLocation->results[2]->formatted_address;
                } catch (Exception  $e) {
                  $googleLocation = null;
                }
                self::$source = 'freegeoip.net';
                self::$error = false;
                return true;
            } catch (Exception  $e) {
                self::$ipInfo = null;
                self::$source = null;
                self::$error = true;
                return false;
            }
        }
    }


}