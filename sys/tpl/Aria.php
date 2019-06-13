<?php
/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/13/2019
 * Time: 21:10
 */

namespace Core\tpl;


use Core\tpl\tonic\Extensions;
use Core\tpl\tonic\Tonic;

class Aria
{

    private $tonic;

    /**
     * Aria constructor.
     * @param $tonic
     */
    public function __construct()
    {
        $this->tonic = new Tonic();

    }

    /**
     * @return Tonic
     */
    public function getTonic(): Tonic
    {
        new Extensions($this->tonic);
        return $this->tonic;


    }



}