<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 09 /Nov, 2020 @ 7:11
 */

namespace Core\joi\System;


use Nette\Utils\Strings;

class Utils
{


    public static function mb_to_gb($mb){
        $mb = Strings::lower($mb);
        $mb = str_replace([' ', 'mb', 'mib'],'', $mb);

        return (float)$mb * (1/1024);
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
        } else {
            return $input;
        }
    }
}