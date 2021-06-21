<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 07 /Jun, 2021 @ 18:07
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\joi\System\Utils;
use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Tools\Timer;

class IpSpamRange extends BaseEventScan implements EventScan
{

    public function scan(): ScanResults
    {
        if (ValkyrieConfig::$PROTECTION_RANGE_IP_SPAM) {

            $runtime = new Timer();
            $runtime->start();
            $this->defaultEventHandler->beforeScan();

            $ip_array = ['24', '186', '189', '190', '200', '201', '202', '209', '212', '213', '217', '222'];

            $range_ip = explode('.', Utils::get_ip_address(), 2);
            if (in_array($range_ip[0], $ip_array, true)) {

                $this->results->setIsThreat(true);
                $message = 'IPs Spam list';
                $this->fireEventLog($message);
                $this->defaultEventHandler->log($message);

                $this->results->setSeverityLevel(4);
                $this->results->setAction(DefaultActions::BLOCK);

            }
            $runtime->finish();
            $this->results->setBadParameters($range_ip);
            $this->results->setParameters([Utils::get_ip_address()]);
            $this->results->setRunTime($runtime->runtime());
            $this->defaultEventHandler->afterScan($this->results);
        }
        return $this->results;
    }
}