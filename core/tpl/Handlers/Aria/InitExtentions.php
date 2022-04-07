<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 10:57
 */

namespace Core\tpl\Handlers\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Contracts\TemplateCompiler;
use Core\tpl\Contracts\TemplateHandler;
use Core\tpl\Extensions\Aria\Extensions;

class InitExtentions  implements TemplateHandler
{

    public function handle(&$content, AriaCompiler $compiler, $lang = 'PHP')
    {

        (new Extensions($compiler));

        $compiler->composer('upper', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return strtoupper($input);
        });
        $compiler->composer('firstupper', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return ucfirst($input);
        });
        $compiler->composer('url', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return str_replace('%2F','\/',$input);
        });
        $compiler->composer('lower', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return strtolower($input);
        });
        $compiler->composer('capitalize', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return ucwords($input);
        });
        $compiler->composer('base64_decode', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return base64_decode($input);
        });
        $compiler->composer('abs', static function($input) {
            if(!is_numeric($input)){
                return $input;
            }
            return abs($input);
        });
        $compiler->composer('isEmpty', static function($input) {
            return empty($input);
        });
        $compiler->composer('truncate', static function($input, $len) {
            if(empty($len)) {
                throw new \Exception('length parameter is required');
            }
            return substr($input,0,$len).(strlen($input) > $len ? '...' : '');
        });
        $compiler->composer('count', static function($input) {
            return count($input);
        });
        $compiler->composer('length', static function($input) {
            return count($input);
        });
        $compiler->composer('toLocal', static function($input) {
            if(!is_object($input)){
                throw new \Exception('variable is not a valid date');
            }
            return date_timezone_set($input, timezone_open(self::$local_tz));
        });
        $compiler->composer('toTz', static function($input, $tz) {
            if(!is_object($input)){
                throw new \Exception('variable is not a valid date');
            }
            return date_timezone_set($input, timezone_open($tz));
        });
        $compiler->composer('toGMT', static function($input, $tz) {
            if(!is_object($input)){
                throw new \Exception('variable is not a valid date');
            }
            if(empty($tz)){
                throw new \Exception('timezone is required');
            }
            return date_timezone_set($input, timezone_open('GMT'));
        });
        $compiler->composer('date', static function($input, $format) {

            if(empty($format)){
                throw new \Exception('date format is required');
            }
            return date($format);
        });
        $compiler->composer('date_format', static function($input, $format) {
            if(!is_object($input)){
                throw new \Exception('variable is not a valid date');
            }
            if(empty($format)){
                throw new \Exception('date format is required');
            }
            return date_format($input,$format);
        });
        $compiler->composer('nl2br', static function($input) {
            return nl2br($input);
        });
        $compiler->composer('stripSlashes', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return stripslashes($input);
        });

        $compiler->composer('substract', static function($input, $val) {
            if(!is_numeric($input) || !is_numeric($val)){
                throw new \Exception('input and value must be numeric');
            }
            return $input - (float)$val;
        });
        $compiler->composer('multiply', static function($input, $val) {
            if(!is_numeric($input) || !is_numeric($val)){
                throw new \Exception('input and value must be numeric');
            }
            return $input * (float)$val;
        });
        $compiler->composer('divide', static function($input, $val) {
            if(!is_numeric($input) || !is_numeric($val)){
                throw new \Exception('input and value must be numeric');
            }
            return $input / (float)$val;
        });
        $compiler->composer('mod', static function($input, $val) {
            if(!is_numeric($input) || !is_numeric($val)){
                throw new \Exception('input and value must be numeric');
            }
            return $input % (float)$val;
        });
        $compiler->composer('encodeTags', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return htmlspecialchars($input,ENT_NOQUOTES);
        });
        $compiler->composer('decodeTags', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return htmlspecialchars_decode($input);
        });
        $compiler->composer('stripTags', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return strip_tags($input);
        });
        $compiler->composer('urlDecode', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return urldecode($input);
        });
        $compiler->composer('addSlashes', static function($input){
            return addslashes($input);
        });
        $compiler->composer('urlFriendly', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return urlencode(self::removeSpecialChars(strtolower($input)));
        });
        $compiler->composer('trim', static function($input) {
            if(!is_string($input)){
                return $input;
            }
            return trim($input);
        });


        $compiler->composer('sha1', static function($input) {
            if(!is_string($input)){
                throw new \Exception('input must be string');
            }
            return sha1($input);
        });
        $compiler->composer('safe', static function($input) {
            return htmlentities($input, ENT_QUOTES);
        });
        $compiler->composer('numberFormat', static function($input, $precision = 2) {
            if(!is_numeric($input)){
                throw new \Exception('input must be numeric');
            }
            return number_format($input,(int)$precision);
        });
        $compiler->composer('lastIndex', static function($input) {
            if(!is_array($input)){
                throw new \Exception('input must be an array');
            }
            return current(array_reverse(array_keys($input)));
        });
        $compiler->composer('lastValue', static function($input) {
            if(!is_array($input)){
                throw new \Exception('input must be an array');
            }
            return current(array_reverse($input));
        });
        $compiler->composer('jsonEncode', static function($input) {
            return json_encode($input);
        });
        $compiler->composer('substr', static function($input, $a = 0, $b = 0) {
            return substr($input,$a,$b);
        });
        $compiler->composer('join', static function($input, $glue) {
            if(!is_array($input)){
                throw new \Exception('input must be an array');
            }
            if(empty($glue)){
                throw new \Exception('string glue is required');
            }
            return implode($glue,$input);
        });
        $compiler->composer('explode', static function($input, $del) {
            if(!is_string($input)){
                throw new \Exception('input must be a string');
            }
            if(empty($del)){
                throw new \Exception('delimiter is required');
            }
            return explode($del,$input);
        });
        $compiler->composer('replace', static function($input, $search, $replace) {
            if(!is_string($input)){
                throw new \Exception('input must be a string');
            }
            if(empty($search)){
                throw new \Exception('search is required');
            }
            if(empty($replace)){
                throw new \Exception('replace is required');
            }
            return str_replace($search,$replace,$input);
        });
        $compiler->composer('preventTagEncode', static function($input) {
            return $input;
        });

        $compiler->composer('default', static function($input, $default) {
            return (empty($input) ? $default : $input);
        });
        $compiler->composer('contextJs', static function($input, $in_str) {
            if( (is_object($input) || is_array($input)) && !$in_str){
                return json_encode($input);
            }

            if(is_numeric($input) || is_bool($input)){
                return $input;
            }

            if($input === null) {
                return 'null';
            }

            if(!$in_str){
                return '"' . addslashes($input) .'"';
            }

            if(is_object($input) || is_array($input)) {
                $input = json_encode($input);
            }
            return addslashes($input);
        });
        $compiler->composer('contextOutTag', static function($input) {
            if(is_object($input) || is_array($input)){
                return $input;
            }

            return htmlentities($input,ENT_QUOTES);
        });

        $compiler->composer('contextTag', static function($input, $in_str) {
            if((is_object($input) || is_array($input)) && $in_str){
                return http_build_query($input);
            }

            if($in_str) {
                return urlencode($input);
            }

            return htmlentities($input,ENT_QUOTES);
        });

        $compiler->composer('addDoubleQuotes', static function($input){
            return '"' . $input . '"';
        });

        $compiler->composer('ifEmpty', static function($input, $true_val, $false_val = null) {
            if(empty($true_val)){
                throw new \Exception('true value is required');
            }
            $ret = $input;
            if(empty($ret)) {
                $ret = $true_val;
            } else if($false_val) {
                $ret = $false_val;
            }
            return $ret;
        });
        $compiler->composer("if", static function($input, $condition, $true_val, $false_val = null, $operator = 'eq') {
            if(empty($true_val)){
                throw new \Exception('true value is required');
            }
            switch($operator){
                case '':
                case '==':
                case '===':
                case '=':
                case 'eq':
                default:
                    $operator= '===';
                    break;
                case '<':
                case 'lt':
                    $operator= '<';
                    break;
                case '>':
                case 'gt':
                    $operator= '>';
                    break;
                case '<=':
                case 'lte':
                    $operator= '<=';
                    break;
                case '>=':
                case 'gte':
                    $operator= '>=';
                    break;
                case 'neq':
                    $operator = '!==';
                    break;
            }
            $ret = $input;
            if(eval('return ("'.$condition.'"'.$operator.'"'.$input.'");')) {
                $ret = $true_val;
            } else if($false_val) {
                $ret = $false_val;
            }
            return $ret;
        });
    }
}