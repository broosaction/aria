<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 07 /Jun, 2021 @ 19:16
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Security\Utils\ValkyrieUtils;
use Core\Tools\Timer;

class ReqMethodProtect extends BaseEventScan implements EventScan
{

    public function scan(): ScanResults
    {
        if (ValkyrieConfig::$PROTECTION_REQUEST_METHOD) {

            $runtime = new Timer();
            $runtime->start();
            $this->defaultEventHandler->beforeScan();

            if (strtolower(ValkyrieUtils::get_request_method()) !== 'get'
                && strtolower(ValkyrieUtils::get_request_method()) !== 'head'
                && strtolower(ValkyrieUtils::get_request_method()) !== 'post'
                && strtolower(ValkyrieUtils::get_request_method()) !== 'patch'
                && strtolower(ValkyrieUtils::get_request_method()) !== 'delete'
                && strtolower(ValkyrieUtils::get_request_method()) !== 'put') {

                $this->results->setIsThreat(true);
                $message = 'Invalid request method '. ValkyrieUtils::get_request_method();
                $this->fireEventLog($message);
                $this->defaultEventHandler->log($message);

                $this->results->setSeverityLevel(8);
                $this->results->setAction(DefaultActions::BLOCK);

            }
            $runtime->finish();
            $this->results->setBadParameters([ValkyrieUtils::get_request_method()]);
            $this->results->setParameters([]);
            $this->results->setRunTime($runtime->runtime());
            $this->defaultEventHandler->afterScan($this->results);
        }
        return $this->results;
    }
}