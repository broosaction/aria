<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 14:17
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\Security\CloudValkyrie\Contracts\SecurityEventHandler;
use Core\Security\CloudValkyrie\Defaults\DefaultEventHandler;
use Core\Security\CloudValkyrie\ScanResults;

class BaseEventScan
{

    public SecurityEventHandler $eventHandler;

    public DefaultEventHandler $defaultEventHandler;

    public bool $intlligentProcess = true;

    public bool $activeLog = true;

    public ScanResults $results;



    public function setClientEventHandler($eventHandler)
    {

        $this->defaultEventHandler = new DefaultEventHandler();

        if($eventHandler !== null){
            $this->eventHandler = $eventHandler;
        }

    }


    public function setIntelligence($toggle = true)
    {
        $this->intlligentProcess = $toggle;
    }

    public function setActiveLog($toggle = true)
    {
        $this->activeLog = $toggle;
    }

    public function setScanResults(ScanResults $results)
    {
       $this->results = $results;
    }

    /**
     * @param SecurityEventHandler $handler
     * @param $event
     */
    public function fireEventLog($message)
    {
        if(isset($this->eventHandler) && $this->eventHandler !== null){

            $this->eventHandler->log($message);

        }
    }
}