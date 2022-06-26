<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 07 /Jun, 2021 @ 20:04
 */

namespace Core\Security\CloudValkyrie\Defaults\RequestScanners;


use Core\Joi\System\Utils;
use Core\Security\CloudValkyrie\Contracts\EventScan;
use Core\Security\CloudValkyrie\Defaults\DefaultActions;
use Core\Security\CloudValkyrie\ScanResults;
use Core\Security\CloudValkyrie\ValkyrieConfig;
use Core\Security\Utils\ValkyrieUtils;
use Core\Tools\Timer;

class IdTheftProtect extends BaseEventScan implements EventScan
{

    public function scan(): ScanResults
    {
        if (ValkyrieConfig::$PROTECTION_IDENTITY_THEFT) {

            $runtime = new Timer();
            $runtime->start();
            $this->defaultEventHandler->beforeScan();

            if (session_id() === '') {
                session_start();
            }

            if (isset($_SESSION['cv_uip'])) {
                $uip = hash('SHA512', ValkyrieUtils::get_user_agent() . 'cv_vc', false);
                if($_SESSION['cv_uip'] !== $uip) {

                    $this->results->setIsThreat(true);

                    if ($this->intlligentProcess) {

                        $message = 'Session hijack';
                        $this->fireEventLog($message);
                        $this->defaultEventHandler->log($message);

                        if (!session_regenerate_id()) {

                            $this->results->setSeverityLevel(8);
                            $this->results->setAction(DefaultActions::BLOCK);
                        }

                        ValkyrieUtils::set_new_server('cv_seschng', 'true');

                    } else {

                        $this->results->setSeverityLevel(8);
                        $this->results->setAction(DefaultActions::BLOCK);
                      //  session_regenerate_id();
                      //  ValkyrieUtils::set_new_server('cv_seschng', 'true');
                    }
                }
            } else {
                $_SESSION['cv_uip'] = hash('SHA512', ValkyrieUtils::get_user_agent() . 'cv_vc', false);
            }
            $runtime->finish();
            $this->results->setBadParameters([ValkyrieUtils::get_user_agent()]);
            $this->results->setParameters([]);
            $this->results->setRunTime($runtime->runtime());
            $this->defaultEventHandler->afterScan($this->results);
        }

        return $this->results;
    }
}