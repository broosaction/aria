<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 07 /Jun, 2021 @ 19:29
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Security\Utils\ValkyrieUtils;
use Core\Tools\Timer;

class SqlProtect  extends BaseEventScan implements EventScan
{

    public function scan(): ScanResults
    {
        if (ValkyrieConfig::$PROTECTION_UNION_SQL) {

            $runtime = new Timer();
            $runtime->start();
            $this->defaultEventHandler->beforeScan();

            $stop = 0;
            $ct_rules = ['*/from/*', '*/insert/*', '+into+', '%20into%20', '*/into/*', ' into ', 'into', '*/limit/*',
                'not123exists*', '*/radminsuper/*', '*/select/*', '+select+', '%20select%20', ' select ', '+union+',
                '%20union%20', '*/union/*', ' union ', '*/update/*', '*/where/*'];

            $check = str_replace($ct_rules, '*', ValkyrieUtils::get_query_string());

            if (ValkyrieUtils::get_query_string() !== $check) {
                $stop++;
            }
            if (preg_match(ValkyrieUtils::get_regex_union(), ValkyrieUtils::get_query_string())) {
                $stop++;
            }
            if (preg_match('/([OdWo5NIbpuU4V2iJT0n]{5}) /', rawurldecode(ValkyrieUtils::get_query_string()))) {
                $stop++;
            }
            if (str_contains(rawurldecode(ValkyrieUtils::get_query_string()), '*')) {
                $stop++;
            }
            if (!empty($stop)) {

                $this->results->setIsThreat(true);
                $message = 'Union attack ';
                $this->fireEventLog($message);
                $this->defaultEventHandler->log($message);

                $this->results->setSeverityLevel(9);
                $this->results->setAction(DefaultActions::BLOCK);

            }
            $runtime->finish();
            $this->results->setBadParameters([]);
            $this->results->setParameters([ValkyrieUtils::get_query_string()]);
            $this->results->setRunTime($runtime->runtime());
            $this->defaultEventHandler->afterScan($this->results);
        }

        return $this->results;
    }
}