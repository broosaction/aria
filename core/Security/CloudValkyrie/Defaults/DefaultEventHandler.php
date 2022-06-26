<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 11:22
 */

namespace Core\Security\CloudValkyrie\Defaults;


use Core\Joi\Start;
use Core\Joi\System\Utils;
use Core\Security\CloudValkyrie\Contracts\SecurityEventHandler;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\Utils\ValkyrieUtils;
use Tracy\Logger;

class DefaultEventHandler implements SecurityEventHandler
{

    public function beforeScan()
    {
        // TODO: Implement beforeScan() method.
    }

    public function afterScan(ScanResults $results)
    {
        if($results->isThreat()){
            $log = new Logger(Start::$SERVERROOT . '/logs');

            $msg = $results->getEvent()." | IP: " . Utils::get_ip_address() . ' ] 
            | DNS: ' . ValkyrieUtils::gethostbyaddr() . " | Agent: " . ValkyrieUtils::get_user_agent() . ' | URL: '
                .  Utils::get_env('REQUEST_URI') . ' | Referer: ' . ValkyrieUtils::get_referer() . "\n\n";

            $log->log($msg, $log::CRITICAL);

        }
    }

    public function log($string)
    {
        // TODO: Implement log() method.
    }

    public function threatFound(ScanResults $results){

    }
}