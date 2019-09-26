<?php


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
        return ($this->finish-$this->start)*10;
    }

    function timescole($time){

        return $time - $this->runtime();

    }



}