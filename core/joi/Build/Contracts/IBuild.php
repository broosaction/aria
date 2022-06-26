<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 31 /May, 2021 @ 23:13
 */

namespace Core\Joi\Build\Contracts;


use Symfony\Component\Console\Output\OutputInterface;

interface IBuild
{
    public function __construct(OutputInterface $output);

    public function build();
}