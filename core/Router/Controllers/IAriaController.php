<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 31 /May, 2021 @ 20:01
 */

namespace Core\Router\Controllers;


use Core\Joi\Start;
use Core\tpl\tonic\Tonic;

interface IAriaController
{


    /**
     * @method GET
     * @path /
     * @param Start $server
     */
    public function __construct(Start $server);



}