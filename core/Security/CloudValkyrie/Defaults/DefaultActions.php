<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 05 /Jun, 2021 @ 15:26
 */

namespace Core\Security\CloudValkyrie\Defaults;


abstract class DefaultActions
{

    public const BLOCK = 'block';
    public const IGNORE = 'ignore';
    public const RATE_LIMIT = 'rate_limit';
    public const ANALYZE = 'analyze';
}