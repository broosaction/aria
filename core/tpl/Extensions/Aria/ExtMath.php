<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/13/2019
 * Time: 21:37
 */

namespace Core\tpl\Extensions\Aria;


use Core\Joi\System\Exceptions\InvalidArgumentException;
use Core\Joi\System\Utils;
use Core\Tools\Calculator\Calculator;
use Core\Tools\Calculator\Tokenizer;
use Core\tpl\Compilers\AriaCompiler;


class ExtMath
{

    private AriaCompiler $ariaCompiler;

    /**
     * Extensions constructor.
     * @param AriaCompiler $ariaCompiler
     */
    public function __construct(AriaCompiler $ariaCompiler)
    {
        $this->ariaCompiler = $ariaCompiler;
        $this->extend();
    }


    private function extend()
    {
        $calculator = new Calculator(new Tokenizer());

        $this->calculator = $calculator;


        $this->ariaCompiler->composer('add', static function ($input, $val) {
            if (!is_numeric($input) || !is_numeric($val)) {
                throw new InvalidArgumentException('input and value must be numeric');
            }
            return $input + (float)$val;
        });

        $this->ariaCompiler->composer('round', static function ($input, $val) {
            $input = (float)$input;
            if (!is_numeric($input) || !is_numeric($val)) {
                throw new InvalidArgumentException('input and value must be numeric');
            }
            return round($input, $val);
        });

        $this->ariaCompiler->composer('math', static function ($input, $val) use ($calculator) {

            if (!isset($val)) {
                throw new InvalidArgumentException('input value must be set');
            }

            return $calculator->calculate($val);
        });

        $this->ariaCompiler->composer('fromMBsizeFormat', static function ($input)  {

            if (!isset($input)) {
                throw new InvalidArgumentException('input value must be set');
            }
            if (!is_numeric($input)) {
                throw new InvalidArgumentException('input and value must be numeric');
            }

            return Utils::pt_size_format(Utils::convertToBytes($input . 'MB'),3);
        });

        $this->ariaCompiler->composer('percent', static function ($input, $a, $b, $c = false)  {

            if (!isset($a, $b)) {
                throw new InvalidArgumentException('input value must be set');
            }

            $normal = (float)$a;
            $current = (float)$b;

            if (!$normal || $normal === $current) {
                return '100';
            }

            $normal = abs($normal);
            $percent = round($current / $normal * 100);
            if (isset($c) && $c === 'true') {
                return 100 - number_format($percent, 0, '.', '');
            }
            return number_format($percent, 0, '.', '');
        });


        $this->ariaCompiler->composer('random', static function ($input, $length = 10, $charlist = '0-9a-z') {

            if (!isset($input)) {

                $charlist = count_chars(preg_replace_callback('#.-.#', function (array $m): string {
                    return implode('', range($m[0][0], $m[0][2]));
                }, $charlist), 3);
                $chLen = strlen($charlist);

                if ($length < 1) {
                    throw new InvalidArgumentException('Length must be greater than zero.');
                }

                if ($chLen < 2) {
                    throw new InvalidArgumentException('Character list must contain at least two chars.');
                }

                $res = '';
                for ($i = 0; $i < $length; $i++) {
                    $res .= $charlist[random_int(0, $chLen - 1)];
                }
                return $res;

            }

            if (!is_array($input)) {
                throw new InvalidArgumentException('Input must be an array');
            }

            if (count($input) === 0) {
                throw new InvalidArgumentException('Array passed has 0 values');
            }

            return $input[random_int(0, count($input) - 1)];
        });

        $this->ariaCompiler->composer('numMassFormat', static function ($input)  {

            if (!isset($input)) {
                throw new InvalidArgumentException('input value must be set');
            }
            if (!is_numeric($input)) {
                throw new InvalidArgumentException('input and value must be numeric');
            }

            return Utils::numMassFormat($input);
        });
    }


}