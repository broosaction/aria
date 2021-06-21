<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 9:24
 */

namespace Core\Security\CloudValkyrie\Scanners;


use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Contracts\ScannerInterface;
use Core\Security\CloudValkyrie\Contracts\SecurityEventHandler;
use Core\Security\CloudValkyrie\Defaults\DefaultEventHandler;
use Core\Security\CloudValkyrie\Defaults\DefaultEvents;
use Core\Security\CloudValkyrie\ScanResults;


class RequestScanner extends Scanner implements ScannerInterface
{


    /**
     * RequestScanner constructor.
     */
    public function __construct()
    {
        $this->scanResults = new ScanResults('RequestScanner');
    }


    /**
     * @param bool $inteligentProcess
     * @param bool $activeLogging
     * @return array
     */
    public function scan($inteligentProcess = true, $activeLogging = true)
    {

        foreach ($this->scanners as $name => $scanner) {

            $handler = $scanner['handle'] ?? $this->getAllEventsHandler();
            //fire the before scan event
            $this->fireEvent($handler, 'before');
            $this->scanResults->setEvent($name);
            //now the scanning starts
            $eventScan = $this->getEventScan($this->scanners[$name]['fn']);
            $eventScan->setClientEventHandler($handler);
            $eventScan->setScanResults($this->scanResults);
            $eventScan->setIntelligence($inteligentProcess);
            $eventScan->setActiveLog($activeLogging);
            $this->scanResults = $eventScan->scan();
            //fire the after scan
            $this->fireEvent($handler, 'after');
            //mark our threats
            if($this->scanResults->isThreat()){
                $this->threats[] = $this->scanResults;
            }

        }
        return $this->threats;
    }


    public function prepareDefaults()
    {

        $this->register(DefaultEvents::PROTECTION_QUERY_STRING, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\QueryString');
        $this->register(DefaultEvents::PROTECTION_SQL, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\SqlInjection');
        $this->register(DefaultEvents::PROTECTION_URL, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\UriProtect');
        $this->register(DefaultEvents::PROTECTION_RANGE_IP_DENY, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\IpDenyRange');
        $this->register(DefaultEvents::PROTECTION_RANGE_IP_SPAM, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\IpSpamRange');
        $this->register(DefaultEvents::PROTECTION_COOKIES, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\CookieProtect');
        $this->register(DefaultEvents::PROTECTION_POST, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\PostProtect');
        $this->register(DefaultEvents::PROTECTION_GET, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\GetProtect');
        $this->register(DefaultEvents::PROTECTION_REQUEST_SERVER, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\RequestServer');
        $this->register(DefaultEvents::PROTECTION_SANTY, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\SantyProtect');
        $this->register(DefaultEvents::PROTECTION_BOTS, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\BotsProtect');
        $this->register(DefaultEvents::PROTECTION_REQUEST_METHOD, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\ReqMethodProtect');
        $this->register(DefaultEvents::PROTECTION_UNION_SQL, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\SqlProtect');
        $this->register(DefaultEvents::PROTECTION_CLICK_ATTACK, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\ClickProtect');
        $this->register(DefaultEvents::PROTECTION_XSS_ATTACK, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\XSSprotect');
        $this->register(DefaultEvents::PROTECTION_IDENTITY_THEFT, 'Core\Security\CloudValkyrie\Defaults\RequestScanners\IdTheftProtect');

    }
}