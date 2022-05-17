<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 01 /Jun, 2021 @ 11:18
 */

namespace App\Bootstrap\Middlewares;



use Core\joi\Start;
use Core\joi\System\Time;

use Core\Router\Http\Middleware\IMiddleware;
use Core\Router\Http\Request;


class IndexMonitor implements IMiddleware
{

    public function handle(Start $server, Request $request): void
    {
       //example of using the sessions in the handler
        $session = $server->getSessions();

        view()->time = new Time();
    }
}