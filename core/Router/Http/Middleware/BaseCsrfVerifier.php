<?php

namespace Core\Router\Http\Middleware;



use Core\Joi\Start;
use Core\Joi\System\Exceptions\TokenMismatchException;
use Core\Router\Http\Request;
use Core\Router\Http\Security\CookieTokenProvider;
use Core\Router\Http\Security\ITokenProvider;

class BaseCsrfVerifier implements IMiddleware
{
    public const POST_KEY = 'csrf_token';
    public const HEADER_KEY = 'X-CSRF-TOKEN';

    protected Start $server;
    /**
     * Urls to ignore. You can use * to exclude all sub-urls on a given path.
     * For example: /admin/*
     * @var array|null
     */
    protected $except;
    /**
     * Urls to include. Can be used to include urls from a certain path.
     * @var array|null
     */
    protected $include;
    protected $tokenProvider;

    /**
     * BaseCsrfVerifier constructor.
     */
    public function __construct()
    {
        $this->tokenProvider = new CookieTokenProvider();
    }

    /**
     * Check if the url matches the urls in the except property
     * @param Request $request
     * @return bool
     */
    protected function skip(Request $request): bool
    {
        if ($this->except === null || count($this->except) === 0) {
            return false;
        }

        foreach($this->except as $url) {
            $url = rtrim($url, '/');
            if ($url[strlen($url) - 1] === '*') {
                $url = rtrim($url, '*');
                $skip = $request->getUrl()->contains($url);
            } else {
                $skip = ($url === rtrim($request->getUrl()->getRelativeUrl(false), '/'));
            }

            if ($skip === true) {

                if(is_array($this->include) === true && count($this->include) > 0) {
                    foreach($this->include as $includeUrl) {
                        $includeUrl = rtrim($includeUrl, '/');
                        if ($includeUrl[strlen($includeUrl) - 1] === '*') {
                            $includeUrl = rtrim($includeUrl, '*');
                            $skip = !$request->getUrl()->contains($includeUrl);
                            break;
                        }

                        $skip = !($includeUrl === rtrim($request->getUrl()->getRelativeUrl(false), '/'));
                    }
                }

                if($skip === false) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Handle request
     *
     * @param Start $server
     * @param Request $request
     * @throws TokenMismatchException
     */
    public function handle(Start $server, Request $request): void
    {
        if ($this->skip($request) === false && $request->isPostBack() === true) {

            $token = $request->getInputHandler()->value(
                static::POST_KEY,
                $request->getHeader(static::HEADER_KEY),
                Request::$requestTypesPost
            );

            if ($this->tokenProvider->validate((string)$token) === false) {
                throw new TokenMismatchException('Invalid CSRF-token.');
            }

        }

        // Refresh existing token
        $this->tokenProvider->refresh();
    }

    public function getTokenProvider(): ITokenProvider
    {
        return $this->tokenProvider;
    }

    /**
     * Set token provider
     * @param ITokenProvider $provider
     */
    public function setTokenProvider(ITokenProvider $provider): void
    {
        $this->tokenProvider = $provider;
    }

    /**
     * @return Start
     */
    public function getServer(): Start
    {
        return $this->server;
    }

    /**
     * @param Start $server
     */
    public function setServer(Start $server): void
    {
        $this->server = $server;
    }



}