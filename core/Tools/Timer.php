<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Core\Tools;


class Timer
{

    private $start;
    private $finish;

    function start(){
        $this->start = microtime(true);
    }

    function finish(){
        $this->finish = microtime(true);
    }

    function runtime() {
        return ($this->finish - $this->start)*10;
    }

    function timescole($time){

        return $time - $this->runtime();

    }



}