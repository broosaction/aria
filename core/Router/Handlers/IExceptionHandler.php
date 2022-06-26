<?php

namespace Core\Router\Handlers;

use Core\Joi\Start;
use Core\Router\Http\Request;
use Exception;


interface IExceptionHandler
{
    /**
     * @param Start $server
     * @param Request $request
     * @param Exception $error
     */
    public function handleError(Start $server, Request $request, Exception $error): void;

}