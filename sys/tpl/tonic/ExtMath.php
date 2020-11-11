<?php
/*
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/13/2019
 * Time: 21:37
 */

namespace Core\tpl\tonic;




//Tonic math extension

use Core\Tools\Calculator\Calculator;
use Core\Tools\Calculator\Tokenizer;

use Exception;

class ExtMath
{

    private $tonic;
    private $calculator;

    /**
     * ExtMath constructor.
     * @param $tonic
     */
    public function __construct($tonic)
    {

        if(!isset($tonic)){

            $tonic = new Tonic(); // dev level view leveler..
        }

        $this->tonic = $tonic;

        $this->extend();
    }


    private function extend()
    {
        $calculator = new Calculator(new Tokenizer());
        
        $this->calculator = $calculator;

        $this->exFunctions();
        $this->tonic::extendModifier('add', static function($input, $val) {
            if(!is_numeric($input) || !is_numeric($val)){
                throw new \Exception('input and value must be numeric');
            }
            return $input + (float)$val;
        });

        $this->tonic::extendModifier('math', static function ($input, $val) use($calculator){

            if(!isset($val) ){
                throw new \Exception('input value must be set');
            }

            return $calculator->calculate($val);
        });

        $this->tonic::extendModifier('percent', static function ($input, $a, $b, $c=false) use($calculator){

            if(!isset($a,$b) ){
                throw new \Exception('input value must be set');
            }

            $normal = (float)$a;
            $current = (float)$b;

            if (!$normal || $normal === $current) {
                return '100';
            }

            $normal = abs($normal);
            $percent = round($current / $normal * 100);
            if(isset($c) && $c==='true'){
                return 100 - number_format($percent, 0, '.', '');
            }
            return number_format($percent, 0, '.', '');
        });


        $this->tonic::extendModifier('random', static function ($input, $length = 10, $charlist = '0-9a-z') {

            if(!isset($input)){

                $charlist = count_chars(preg_replace_callback('#.-.#', function (array $m): string {
                    return implode('', range($m[0][0], $m[0][2]));
                }, $charlist), 3);
                $chLen = strlen($charlist);

                if ($length < 1) {
                    throw new Exception('Length must be greater than zero.');
                }

                if ($chLen < 2) {
                    throw new Exception('Character list must contain at least two chars.');
                }

                $res = '';
                for ($i = 0; $i < $length; $i++) {
                    $res .= $charlist[random_int(0, $chLen - 1)];
                }
                return $res;

            }

            if(!is_array($input)){
                throw new Exception('Input must be an array');
            }

            if(count($input)===0){
                throw new Exception('Array passed has 0 values');
            }

            return $input[random_int(0,count($input)-1)];
        });
    }
    
    private function exFunctions()
    {
        if (!isset($this->calculator)) {

            $this->calculator = new Calculator(new Tokenizer()); // dev level view leveler. shooul not run
        }


    }

}