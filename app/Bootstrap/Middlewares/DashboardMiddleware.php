<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 04 /Jun, 2021 @ 9:23
 */

namespace App\Bootstrap\Middlewares;


use App\Models\Header;
use Core\joi\Start;
use Core\joi\System\Time;
use Core\Router\Http\Middleware\IMiddleware;
use Core\Router\Http\Request;

class DashboardMiddleware implements IMiddleware
{

    public function handle(Start $server, Request $request): void
    {

    }
}