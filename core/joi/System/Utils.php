<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 09 /Nov, 2020 @ 7:11
 */

namespace Core\Joi\System;


use Nette\Utils\Strings;

class Utils
{

    private static array $size_unites = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZP', 'YB'];


    /**
     * converts MB size to GB, as these are the most common as of today
     * @param $mb
     * @return float|int
     */
    public static function mb_to_gb($mb)
    {
        $mb = Strings::lower($mb);
        $mb = str_replace([' ', 'mb', 'mib'], '', $mb);

        return (float)$mb * (1 / 1024);
    }


    /**
     * converts a given bytes value to a human readable value.
     * @param $bytes
     * @param int $dec
     * @return string
     */
    public static function pt_size_format($bytes, $dec = 2): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        $factor = floor(log($bytes, 1024));
        return sprintf("%.{$dec}f", $bytes / (1024 ** $factor)) . ' ' . self::$size_unites[$factor];
    }

    public static function bytesToGB($bytes) {
        if ($bytes < 0) {
            return 0;
        }
        return round($bytes / 1073741824, 2);
    }


    /**
     * converts a human readable size i.e 3GB to bytes.
     * @param string $from
     * @return bool|float
     */
    public static function convertToBytes(string $from)
    {

        $from = trim(self::ensureUTF8($from));
        //Get suffix
        $suffix = Strings::upper(trim(substr($from, -2)));
        //check for one char suffix
        if ((int)$suffix !== 0) {
            $suffix = 'B';
        }
        if (!in_array($suffix, self::$size_unites, true)) {
            return false;
        }
        $number = trim(substr($from, 0, strlen($from) - strlen($suffix)));
        if (!is_numeric($number)) {
            //Allow only floats and integers
            return false;
        }
        return (float)($number * (1024 ** array_flip(self::$size_unites)[$suffix]));
    }

    /**
     * Returns a pseudo-random v4 UUID.
     *
     * This function is based on a comment by Andrew Moore on php.net
     *
     * @see http://www.php.net/manual/en/function.uniqid.php#94959
     *
     * @return string
     */
    public static function getUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Checks if a string is a valid UUID.
     *
     * @param string $uuid
     *
     * @return bool
     */
    public static function validateUUID($uuid)
    {
        return 0 !== preg_match(
                '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i',
                $uuid
            );
    }

    /**
     * This method takes an input string, checks if it's not valid UTF-8 and
     * attempts to convert it to UTF-8 if it's not.
     *
     * Note that currently this can only convert ISO-8859-1 to UTF-8 (latin-1),
     * anything else will likely fail.
     *
     * @param string $input
     *
     * @return string
     */
    public static function ensureUTF8($input)
    {
        $encoding = mb_detect_encoding($input, ['UTF-8', 'ISO-8859-1'], true);

        if ('ISO-8859-1' === $encoding) {
            return utf8_encode($input);
        }

        return $input;
    }


    /**
     * checks if a given domain name have a given TXT record and value
     * @param $txt_record
     * @param $domain
     * @param $compare
     * @return bool
     */
    public static function _checkDNSTxtRecord($txt_record, $domain, $compare): bool
    {
        if (checkdnsrr("$txt_record.$domain", "TXT")) {
            $result = dns_get_record("$txt_record.$domain", DNS_TXT);

            if (isset($result[0]["txt"]) && $result[0]["txt"] === $compare) {
                return true;
            }

        }
        return false;
    }


    /**
     * used to resize a given image and also compresses
     * @param $max_width
     * @param $max_height
     * @param $source_file
     * @param $dst_dir
     * @param int $quality
     * @return bool
     */
    public static function resize_Crop_Image($max_width, $max_height, $source_file, $dst_dir, $quality = 80)
    {
        $imgsize = @getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];
        switch ($mime) {
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;
            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                break;
            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                break;
            default:
                return false;
                break;
        }
        $dst_img = @imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);
        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        if ($width_new > $width) {
            $h_point = (($height - $height_new) / 2);
            @imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        } else {
            $w_point = (($width - $width_new) / 2);
            @imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }
        @imagejpeg($dst_img, $dst_dir, $quality);
        if ($dst_img) {
            @imagedestroy($dst_img);
        }
        if ($src_img) {
            @imagedestroy($src_img);
        }
    }

    /**
     * used to compress a given image
     * @param $source_url
     * @param $destination_url
     * @param int $quality
     */
    public static function compressImage($source_url, $destination_url, $quality = 80): void
    {
        $info = getimagesize($source_url);
        if ($info['mime'] === 'image/jpeg') {
            $image = @imagecreatefromjpeg($source_url);
            @imagejpeg($image, $destination_url, $quality);
        } elseif ($info['mime'] === 'image/gif') {
            $image = @imagecreatefromgif($source_url);
            @imagegif($image, $destination_url);
        } elseif ($info['mime'] === 'image/png') {
            $image = @imagecreatefrompng($source_url);
            @imagepng($image, $destination_url);
        }
    }


    /**
     * turn plain links in text to html intractable links
     * @param $text
     * @param bool $link
     * @return string|string[]
     */
    public static function markUp($text, $link = true)
    {
        if ($link === true) {
            $link_search = '/\[a\](.*?)\[\/a\]/i';
            if (preg_match_all($link_search, $text, $matches)) {
                foreach ($matches[1] as $match) {
                    $match_decode = urldecode($match);
                    $match_decode_url = $match_decode;
                    $count_url = mb_strlen($match_decode);
                    if ($count_url > 50) {
                        $match_decode_url = mb_substr($match_decode_url, 0, 30) . '....' . mb_substr($match_decode_url, 30, 20);
                    }
                    $match_url = $match_decode;
                    if (!preg_match("/http(|s):\/\//", $match_decode)) {
                        $match_url = 'http://' . $match_url;
                    }
                    $text = str_replace('[a]' . $match . '[/a]', '<a href="' . strip_tags($match_url) . '" target="_blank" class="hash" rel="nofollow">' . $match_decode_url . '</a>', $text);
                }
            }
        }

        $link_search = '/\[img\](.*?)\[\/img\]/i';
        if (preg_match_all($link_search, $text, $matches)) {
            foreach ($matches[1] as $match) {
                $match_decode = urldecode($match);
                $text = str_replace('[img]' . $match . '[/img]', '<a href="' . getMedia(strip_tags($match_decode)) . '" target="_blank"><img style="width:300px;border-radius: 20px;" src="' . getMedia(strip_tags($match_decode)) . '"></a>', $text);
            }
        }
        return $text;
    }

    /**
     * used to get the real IP address of the visitor
     * @return mixed|string
     */
    public static function get_ip_address()
    {
        if (!empty($_SERVER['http-cf-connecting-ip'])) {
            return $_SERVER['http-cf-connecting-ip'];
        }
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($iplist as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            } else {
                if (filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && filter_var($_SERVER['HTTP_X_FORWARDED'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && filter_var($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
            return $_SERVER['HTTP_FORWARDED'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }


    /**
     * used to show an easy human readable number e.i 110000 to 11k
     * @param $num
     * @return string
     */
    public static function numMassFormat($num)
    {

        if ($num > 1000) {

            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = array('K', 'M', 'B', 'T');
            $x_count_parts = count($x_array) - 1;
            $x_display = $x_array[0] . ((int)$x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];

            return $x_display;

        }

        return $num;
    }


    /**
     * checks if a given {@see $ip} belongs a given group of ips {@see $range}
     * @param $ip
     * @param $range
     * @return bool
     */
    public static function ip_in_range($ip, $range)
    {
        if (strpos($range, '/') === false) {
            $range .= '/32';
        }
        // $range is in IP/CIDR format eg 127.0.0.1/24
        [$range, $netmask] = explode('/', $range, 2);
        $range_decimal = ip2long($range);
        $ip_decimal = ip2long($ip);
        $wildcard_decimal = (2 ** (32 - $netmask)) - 1;
        $netmask_decimal = ~$wildcard_decimal;
        return (($ip_decimal & $netmask_decimal) === ($range_decimal & $netmask_decimal));
    }

    /** functions base
     * @param $st_var
     * @return string
     */
    public static function get_env($st_var): string
    {
        global $HTTP_SERVER_VARS;
        if (isset($_SERVER[$st_var])) {
            return strip_tags($_SERVER[$st_var]);
        }

        if (isset($_ENV[$st_var])) {
            return strip_tags($_ENV[$st_var]);
        }

        if (isset($HTTP_SERVER_VARS[$st_var])) {
            return strip_tags($HTTP_SERVER_VARS[$st_var]);
        }

        if (getenv($st_var)) {
            return strip_tags(getenv($st_var));
        }

        if (function_exists('apache_getenv') && apache_getenv($st_var, true)) {
            return strip_tags(apache_getenv($st_var, true));
        }
        return '';
    }

    public static function normalizeFileNameString($str = '')
    {
        //https://stackoverflow.com/a/19018736
        $str = strip_tags($str);
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode($str, ENT_QUOTES, "utf-8");
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        return $str;
    }


    public static function set_env($st_var, $data)
    {
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
        if (function_exists('apache_getenv') && apache_getenv($st_var, true)) {
            apache_setenv($st_var, $data, true);
            return 1;
        }
        return 0;
    }

    public static function compress($source, $destination, $quality)
    {

        $info = getimagesize($source);

        if ($info['mime'] === 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
            imagejpeg($image, $destination, $quality);
        } elseif ($info['mime'] === 'image/gif') {
            $image = imagecreatefromgif($source);
            imagegif($image, $destination, $quality);
        } elseif ($info['mime'] === 'image/png') {
            $image = imagecreatefrompng($source);
            imagepng($image, $destination, $quality);
        }

        return $destination;
    }


    public static function dbTypeToLocal($type)
    {
        if (str_contains($type, 'int')) {
            return 'int';
        }

        if (str_contains($type, 'text')) {
            return 'string';
        }

        if (str_contains($type, 'var')) {
            return 'string';
        }

        return 'string';
    }

    public static function quickGetFileContents($url, $ref=''){
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n" .
                   // "Cookie: senturl=".$url."\r\n" .
                    "referer: ".$ref."\r\n"
            )
        );


        return file_get_contents($url, false, stream_context_create($arrContextOptions));
    }


    public static function urlMask($url){
        $_url = parse_url($url);
        if($_url['scheme'] === 'https'){
            $url = str_replace('https://','0https0',$url);
        }else{
            $url = str_replace('http://','0http0',$url);
        }
        return str_replace('.','0-0', $url);
    }


    public static function urlUnMask($url){

        if(str_contains($url, '0https0')){
            $url = str_replace('0https0','https://',$url);
        }else{
            $url = str_replace('0http0','http://',$url);
        }

        return str_replace('0-0','.', $url);
    }


}