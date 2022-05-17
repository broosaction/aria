<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 04 /Jun, 2021 @ 14:34
 */

namespace App\Bootstrap\Events;


use Core\joi\Start;
use Core\Router\Event\EventArgument;
use Core\Router\Handlers\EventsArgumentsHandler;
use Core\Security\CloudValkyrie\Defaults\DefaultEvents;
use Core\Security\CloudValkyrie\ValkyrieConfig;

class EventInit implements EventsArgumentsHandler
{

    /**
     * Fires when router is initializing and before routes are loaded.
     * a best place to set custom CloufValkyrie event handlers
     * @param Start $server
     * @param EventArgument $argument
     */
    public function handle(Start $server, EventArgument $argument): void
    {

    }
}