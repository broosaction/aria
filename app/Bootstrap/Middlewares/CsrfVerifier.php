<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 01 /Jun, 2021 @ 9:53
 */

namespace App\Bootstrap\Middlewares;


use Core\Router\Http\Middleware\BaseCsrfVerifier;

class CsrfVerifier extends BaseCsrfVerifier
{
    /**
     * CSRF validation will be ignored on the following urls.
     */
    protected $except = ['/api/*'];


}