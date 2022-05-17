<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 04 /Jun, 2021 @ 14:28
 */

namespace App\Bootstrap\Events;


use Core\joi\Start;
use Core\Router\Event\EventArgument;
use Core\Router\Handlers\EventsArgumentsHandler;

class EventAll implements EventsArgumentsHandler
{

    /**
     * Fires when a event is triggered.
     * @param Start $server
     * @param EventArgument $argument
     */
    public function handle(Start $server, EventArgument $argument): void
    {
        // TODO: Implement handle() method.
    }
}