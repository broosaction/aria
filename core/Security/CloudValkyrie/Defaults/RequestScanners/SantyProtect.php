<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 07 /Jun, 2021 @ 18:55
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\Joi\System\Utils;
use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Tools\Timer;

class SantyProtect extends BaseEventScan implements EventScan
{

    public function scan(): ScanResults
    {
        if (ValkyrieConfig::$PROTECTION_SANTY) {

            $runtime = new Timer();
            $runtime->start();
            $this->defaultEventHandler->beforeScan();

            $ct_rules = ['rush', 'highlight=%', 'perl', 'chr(', 'pillar', 'visualcoder', 'sess_'];
            $check = str_replace($ct_rules, '*', strtolower(Utils::get_env('REQUEST_URI')));

            if (strtolower(Utils::get_env('REQUEST_URI')) !== $check) {

                $this->results->setIsThreat(true);
                $message = 'Santy';
                $this->fireEventLog($message);
                $this->defaultEventHandler->log($message);

                $this->results->setSeverityLevel(5);
                $this->results->setAction(DefaultActions::BLOCK);

            }
            $runtime->finish();
            $this->results->setBadParameters([$check]);
            $this->results->setParameters([Utils::get_env('REQUEST_URI')]);
            $this->results->setRunTime($runtime->runtime());
            $this->defaultEventHandler->afterScan($this->results);
        }
        return $this->results;
    }
}