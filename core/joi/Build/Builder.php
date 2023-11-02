<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 31 /May, 2021 @ 23:30
 */

namespace Core\Joi\Build;


use Core\Joi\Build\Contracts\IBuild;
use Symfony\Component\Console\Output\OutputInterface;

class Builder implements IBuild
{

    protected OutputInterface $output;
    protected $dir;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;

        $this->dir = str_replace('\core\Joi\Build', '', __DIR__);
    }

    public function build()
    {
        // TODO: Implement build() method.
    }
}