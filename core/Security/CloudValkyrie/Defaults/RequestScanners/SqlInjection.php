<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 15:42
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\Joi\System\Utils;
use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Tools\Timer;

class SqlInjection extends BaseEventScan implements EventScan
{
    public function scan(): ScanResults
    {
        if (ValkyrieConfig::$PROTECTION_UNION_SQL) {
            $runtime = new Timer();
            $runtime->start();
            $this->defaultEventHandler->beforeScan();

            $pattern = '#select|update|delete|concat|create|table|union|length|show_table|mysql_list_tables|mysql_list_fields|mysql_list_dbs#i';
            $matches = [];

            $url = Utils::get_env('REQUEST_URI');
            if (preg_match($pattern, $url, $matches)) {

                $message = 'SQL injection attack';
                $this->fireEventLog($message);
                $this->defaultEventHandler->log($message);

                $this->results->setSeverityLevel(10);
                $this->results->setIsThreat(true);
                $this->results->setAction(DefaultActions::BLOCK);
            }
            $runtime->finish();
            $this->results->setBadParameters($matches);
            $this->results->setParameters([$url]);
            $this->results->setRunTime($runtime->runtime());
            $this->defaultEventHandler->afterScan($this->results);
        }
        return $this->results;
    }
}