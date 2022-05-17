<?php
/*
 * Copyright (c) 2022.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace App\Bootstrap\Controllers\Files\Uptime;


class Check
{


    /**
     * @method ['get', 'post']
     * @path /uptime/check.app
     * @id UptimeCheck
     * defaultParameterRegex .*?
     */
    public function upTimeCheckApp()
    {
        echo 'hello';
    }

}