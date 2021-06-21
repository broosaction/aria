<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 08 /Jun, 2021 @ 15:45
 */

namespace Core\Security;


use phpseclib\Crypt\Hash;

class Crypto
{

    /**
     * @param string $message The message to authenticate
     * @param string $password Password to use (defaults to client `secret` )
     * @return string Calculated HMAC
     */
    public function calculateHMAC(string $message, string $password = ''): string
    {
        if ($password === '') {
            $password = 'xbasexbase250xxx';
        }

        // Append an "a" behind the password and hash it to prevent reusing the same password as for encryption
        $password = hash('sha512', $password . 'a');

        $hash = new Hash('sha512');
        $hash->setKey($password);
        return $hash->hash($message);
    }


}