<?php
/**
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Nov, 2020 @ 10:50
 */

namespace Core\Tools\Security;


class SecureRandom
{
    /**
     * Generate a random string of specified length.
     * @param int $length The length of the generated string
     * @param string $characters An optional list of characters to use if no character list is
     * 							specified all valid base64 characters are used.
     * @return string
     */
    public function generate(int $length,
                             string $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'): string {
        $maxCharIndex = \strlen($characters) - 1;
        $randomString = '';

        while ($length > 0) {
            $randomNumber = \random_int(0, $maxCharIndex);
            $randomString .= $characters[$randomNumber];
            $length--;
        }
        return $randomString;
    }

}