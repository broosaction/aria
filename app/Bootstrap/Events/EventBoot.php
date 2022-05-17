<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 04 /Jun, 2021 @ 14:50
 */

namespace App\Bootstrap\Events;


use App\Scripts\AriaExtentions\CustomAriaExtentions;
use App\Scripts\AriaExtentions\CustomLatteExtensions;
use Core\joi\Start;
use Core\Router\Event\EventArgument;
use Core\Router\Handlers\EventsArgumentsHandler;

class EventBoot implements EventsArgumentsHandler
{

    /**
     * Fires when the router is booting.
     * This happens just before boot-managers are rendered and before any routes has been loaded.
     * @param Start $server
     * @param EventArgument $argument
     */
    public function handle(Start $server, EventArgument $argument): void
    {
        // TODO: Implement handle() method.
    }
}