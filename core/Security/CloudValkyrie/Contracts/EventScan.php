<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 13:19
 */

namespace Core\Security\CloudValkyrie\Contracts;


use Core\Security\CloudValkyrie\Defaults\DefaultEventHandler;
use Core\Security\CloudValkyrie\ScanResults;

interface EventScan
{
    public function scan(): ScanResults;

    public function setClientEventHandler(SecurityEventHandler $eventHandler);

    public function setIntelligence($toggle = true);

    public function setActiveLog($toggle = true);

    public function setScanResults(ScanResults $results);

}