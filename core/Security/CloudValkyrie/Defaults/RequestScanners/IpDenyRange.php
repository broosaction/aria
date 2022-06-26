<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 07 /Jun, 2021 @ 17:57
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\Joi\System\Utils;
use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Security\Utils\ValkyrieUtils;
use Core\Tools\Timer;

class IpDenyRange  extends BaseEventScan implements EventScan
{


    public function scan(): ScanResults
    {
        if (ValkyrieConfig::$PROTECTION_RANGE_IP_DENY) {

            $runtime = new Timer();
            $runtime->start();
            $this->defaultEventHandler->beforeScan();

            $ip_array = ['0', '1', '2', '5', '10', '14', '23', '27', '31', '36', '37', '39', '42', '46', '49', '50',
                '100', '101', '102', '103', '104', '105', '106', '107', '114', '172', '176', '177', '179', '181',
                '185', '192', '223', '224'];

            $range_ip = explode('.', Utils::get_ip_address());
            if (in_array($range_ip[0], $ip_array, false)) {

                $this->results->setIsThreat(true);
                $message = 'IPs reserved list';
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