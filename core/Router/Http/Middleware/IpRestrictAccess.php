<?php

namespace Core\Router\Http\Middleware;

use Core\Joi\System\Exceptions\HttpException;
use Core\Router\Http\Request;



abstract class IpRestrictAccess implements IMiddleware
{
    protected $ipBlacklist = [];
    protected $ipWhitelist = [];

    protected function validate(string $ip): bool
    {
        // Accept ip that is in white-list
        if(in_array($ip, $this->ipWhitelist, true) === true) {
            return true;
        }

        foreach ($this->ipBlacklist as $blackIp) {

            // Blocks range (8.8.*)
            if ($blackIp[strlen($blackIp) - 1] === '*' && strpos($ip, trim($blackIp, '*')) === 0) {
                return false;
            }

            // Blocks exact match
            if ($blackIp === $ip) {
                return false;
            }

        }

        return true;
    }

    /**
     * @param Request $request
     * @throws HttpException
     */
    public function handle(Request $request): void
    {
        if($this->validate((string)$request->getIp()) === false) {
            throw new HttpException(sprintf('Restricted ip. Access to %s has been blocked', $request->getIp()), 403);
        }
    }
}