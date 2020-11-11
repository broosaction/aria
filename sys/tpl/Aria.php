<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/13/2019
 * Time: 21:10
 */

namespace Core\tpl;


use Core\joi\Start;
use Core\tpl\tonic\Extensions;
use Core\tpl\tonic\Tonic;

class Aria
{

    private $tonic;
    private $server;

    /**
     * Aria constructor.
     * @param Start $server
     */
    public function __construct(Start $server)
    {
        $this->tonic = new Tonic();
        $this->server = $server;
    }

    /**
     * @return Tonic
     */
    public function getTonic(): Tonic
    {
        new Extensions($this->tonic, $this->server);
        return $this->tonic;


    }



}