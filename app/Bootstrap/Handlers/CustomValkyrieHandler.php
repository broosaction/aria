<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 06 /Jun, 2021 @ 18:51
 */

namespace App\Bootstrap\Handlers;


use Core\Security\CloudValkyrie\Contracts\SecurityEventHandler;
use Core\Security\CloudValkyrie\ScanResults;

class CustomValkyrieHandler implements SecurityEventHandler
{

    public function beforeScan()
    {
        // TODO: Implement beforeScan() method.
    }

    public function afterScan(ScanResults $results)
    {
        // TODO: Implement afterScan() method.
        if($results->isThreat()){

        }


    }

    public function log($string)
    {
        // TODO: Implement log() method.
    }
}