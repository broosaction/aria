<?php

namespace Core\Router\Handlers;

use Closure;
use Core\Joi\Start;
use Core\Router\Http\Request;
use Exception;


/**
 * Class CallbackExceptionHandler
 *
 * Class is used to create callbacks which are fired when an exception is reached.
 * This allows for easy handling 404-exception etc. without creating an custom ExceptionHandler.
 *
 * @package \Pecee\SimpleRouter\Handlers
 */
class CallbackExceptionHandler implements IExceptionHandler
{

    protected $callback;

    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param Start $server
     * @param Request $request
     * @param Exception $error
     */
    public function handleError(Start $server, Request $request, Exception $error): void
    {
        /* Fire exceptions */
        call_user_func($this->callback,
            $server,
            $request,
            $error
        );
    }
}