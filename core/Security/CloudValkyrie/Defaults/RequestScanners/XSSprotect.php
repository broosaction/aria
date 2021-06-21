<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 07 /Jun, 2021 @ 19:54
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Security\Utils\ValkyrieUtils;
use Core\Tools\Timer;

class XSSprotect extends BaseEventScan implements EventScan
{

    public function scan(): ScanResults
    {
        if (ValkyrieConfig::$PROTECTION_XSS_ATTACK) {

            $runtime = new Timer();
            $runtime->start();
            $this->defaultEventHandler->beforeScan();

            $ct_rules = ['eval', 'xmlns', 'xlink:href', 'FScommand', 'style', 'http\:\/\/', 'https\:\/\/', 'cmd=',
                '&cmd', 'exec', 'concat', './', '../', 'http:', 'h%20ttp:', 'ht%20tp:', 'htt%20p:', 'http%20:', 'https:',
                'h%20ttps:', 'ht%20tps:', 'htt%20ps:', 'http%20s:', 'https%20:', 'ftp:', 'f%20tp:', 'ft%20p:', 'ftp%20:',
                'ftps:', 'f%20tps:', 'ft%20ps:', 'ftp%20s:', 'ftps%20:', '.php?url='];

            $check = str_replace($ct_rules, '*', ValkyrieUtils::get_query_string());
            if (ValkyrieUtils::get_query_string() !== $check) {

                $this->results->setIsThreat(true);
                $message = 'XSS attack';
                $this->fireEventLog($message);
                $this->defaultEventHandler->log($message);

                if ($this->intlligentProcess) {

                    ValkyrieUtils::set_query_string($check);

                    $this->results->setSeverityLevel(2);
                    $this->results->setAction(DefaultActions::ANALYZE);

                    if (ValkyrieUtils::get_query_string() !== $check) {
                        $this->results->setSeverityLevel(7);
                        $this->results->setAction(DefaultActions::BLOCK);
                    }

                } else {

                    $this->results->setSeverityLevel(7);
                    $this->results->setAction(DefaultActions::BLOCK);

                }
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