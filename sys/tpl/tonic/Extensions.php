<?php
/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/13/2019
 * Time: 21:35
 */

namespace Core\tpl\tonic;


class Extensions
{

    private $tonic;

    /**
     * Extensions constructor.
     * @param $tonic
     */
    public function __construct($tonic)
    {
        if(!isset($tonic)){

            $tonic = new Tonic(); // dev level view leveler.
        }
        $this->tonic = $tonic;

        new ExtMath($this->tonic);

        $this->extendTonic();
    }


    private function extendTonic()
    {

        $this->tonic::extendModifier('test', static function($input){
            return 'Aria works';
        });

    }
}