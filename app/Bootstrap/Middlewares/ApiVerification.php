<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 01 /Jun, 2021 @ 9:58
 */

namespace App\Bootstrap\Middlewares;


use Core\joi\Start;
use Core\Router\Http\Middleware\IMiddleware;
use Core\Router\Http\Request;

class ApiVerification implements IMiddleware
{
    public function handle(Start $server, Request $request): void
    {
        // Do authentication
        $request->authenticated = true;

        if ($request->getUrl()->contains('/api')) {
            $this->check($server, $request);
        }
    }

    private function check(Start $server, Request $request)
    {
        header('Content-Type: application/json; charset=utf-8');
        error_reporting(JSON_ERROR_NONE);

        if (!isset($_REQUEST['key'])) {
            response()->json([
                'status' => 'error',
                'type' => 'empty',
                'message' => 'API key is not set, please parse your API key',
                'data' => 'empty',
                'joi' => 'Joi'
            ]);
        }

    }

}