<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 04 /Jun, 2021 @ 14:26
 */

namespace Core\Router\Handlers;


use Core\Joi\Start;
use Core\Router\Event\EventArgument;


interface EventsArgumentsHandler
{
    /**
     * @param Start $server
     * @param EventArgument $argument
     */
    public function handle(Start $server, EventArgument $argument): void;
}