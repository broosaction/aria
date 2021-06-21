<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 14:16
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;



use Core\joi\System\Utils;
use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Tools\Timer;

class QueryString extends BaseEventScan implements EventScan
{

    public function scan(): ScanResults
    {
        $runtime = new Timer();
        $runtime->start();
        $this->defaultEventHandler->beforeScan();
        $queryString = Utils::get_env('QUERY_STRING');
        $matches = [];
        if ($queryString !== '' && !preg_match('/^[_a-zA-Z0-9-=&]+$/', $queryString, $matches)) {
            $this->results->setIsThreat(true);

                $message = 'Query String went in';
                $this->fireEventLog($message);
                $this->defaultEventHandler->log($message);

                if(isset($_REQUEST['ua']) || isset($_REQUEST['run']) || isset($_REQUEST['w']) || isset($_REQUEST['_tracy_bar'])){
                    $this->results->setSeverityLevel(2);
                }else{
                    $this->results->setSeverityLevel(8);
                    $this->results->setAction(DefaultActions::ANALYZE);
                }


        }
        $runtime->finish();
        $this->results->setBadParameters($matches);
        $this->results->setParameters([$queryString]);
        $this->results->setRunTime($runtime->runtime());
        $this->defaultEventHandler->afterScan($this->results);

        return $this->results;
    }

}