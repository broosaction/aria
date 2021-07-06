<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 02 /Jun, 2021 @ 13:56
 */

namespace Core\Router\Controllers;


use Core\joi\Start;
use Core\joi\System\Exceptions\BadMethodCallException;

class BaseController implements IAriaController
{

    /**
     * @var Start
     */
    protected Start $server;

    /**
     * BaseController constructor.
     * @param Start $server
     */
    public function __construct(Start $server)
    {
        $this->server = $server;
    }

    /**
     * Execute an action on the controller.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function callAction(string $method, array $parameters)
    {
        return call_user_func_array([$this, $method], $parameters);
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }

}