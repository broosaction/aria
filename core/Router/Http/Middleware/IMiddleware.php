<?php

namespace Core\Router\Http\Middleware;

use Core\joi\Start;
use Core\Router\Http\Request;

interface IMiddleware
{
    /**
     * @param Start $server
     * @param Request $request
     */
    public function handle(Start $server, Request $request): void;

}