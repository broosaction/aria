<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 02 /Jun, 2021 @ 21:32
 */

namespace App\Bootstrap\Controllers\Files;


use Core\Router\Controllers\BaseController;

class Ads extends BaseController
{
    /**
     * @method ['get', 'post']
     * @path /ads.txt
     * @id adsTxt
     * defaultParameterRegex .*?
     */
    public function adsTxt()
    {

        echo 'google.com, pub-5699394806007989, DIRECT, f08c47fec0942fa0';

    }
}