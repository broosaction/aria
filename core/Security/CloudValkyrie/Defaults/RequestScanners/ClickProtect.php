<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 07 /Jun, 2021 @ 19:43
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Security\Utils\ValkyrieUtils;
use Core\Tools\Timer;

class ClickProtect extends BaseEventScan implements EventScan
{

    public function scan(): ScanResults
    {
        if (ValkyrieConfig::$PROTECTION_CLICK_ATTACK) {

            $runtime = new Timer();
            $runtime->start();
            $this->defaultEventHandler->beforeScan();

            $ct_rules = array('/*', 'c2nyaxb0', '/*');

            if (ValkyrieUtils::get_query_string() !== str_replace($ct_rules, '*', ValkyrieUtils::get_query_string())) {

                $this->results->setIsThreat(true);
                $message = 'Click attack';
                $this->fireEventLog($message);
                $this->defaultEventHandler->log($message);

                $this->results->setSeverityLevel(7);
                $this->results->setAction(DefaultActions::BLOCK);

            }

            $runtime->finish();
            $this->results->setBadParameters([ValkyrieUtils::get_query_string()]);
            $this->results->setParameters([]);
            $this->results->setRunTime($runtime->runtime());
            $this->defaultEventHandler->afterScan($this->results);
        }

        return $this->results;
    }
}