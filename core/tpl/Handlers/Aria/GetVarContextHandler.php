<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 4:51
 */

namespace Core\tpl\Handlers\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Compilers\BaseTemplateCompiler;
use Core\tpl\Contracts\TemplateCompiler;

class GetVarContextHandler implements \Core\tpl\Contracts\TemplateHandler
{
    private $_context;

    public function handle(&$content, AriaCompiler $compiler, $lang = 'PHP')
    {
        $context = $compiler->getProperty(BaseTemplateCompiler::CUR_CONTEXT);
        if($context === null) {
            $cont = $content;
            $in_str = false;
            $str_char = '';
            $in_tag = false;
            $prev_tag = '';
            $prev_char = '';
        } else {
            $cont = substr($content,$context['offset']);
            $in_str = $context['in_str'];
            $str_char = $context['str_char'];
            $in_tag = $context['in_tag'];
            $prev_tag = $context['tag'];
            $prev_char = $context['prev_char'];
        }
        $i = strpos($cont, $content);
        if($i === false){
            return false;
        }
        $escaped = false;
        $capturing_tag_name = false;
        $char = '';
        for($j = 0; $j <= $i; $j++){
            $prev_char = $char;
            $char = $cont[$j];
            switch($char){
                case "\\":
                    $escaped = true;
                    continue 2;
                    break;
                case "'":
                case '"':
                    if(!$escaped){
                        if($in_str && $char === $str_char) {
                            $str_char = $char;
                        }
                        $in_str = !$in_str;
                    }
                    break;
                case '>':
                    if(!$in_str){
                        if($prev_char === '?'){
                            continue 2;
                        }
                        $in_tag = false;
                        if($capturing_tag_name) {
                            $capturing_tag_name = false;
                        }
                    }
                    break;
                case '<':
                    if(!$in_str){
                        if($cont[$j + 1] === '?'){
                            continue 2;
                        }
                        $prev_tag = "";
                        $in_tag = true;
                        $capturing_tag_name = true;
                        continue 2;
                    }
                    break;
                case ' ':
                    if($capturing_tag_name){
                        $capturing_tag_name = false;
                    }
                    break;
                default:
                    if($capturing_tag_name){
                        $prev_tag .= $char;
                    }
            }
            if($escaped) {
                $escaped = false;
            }
        }

        $context['offset'] = $context['offset'] ?? '';
        $this->_context = array(
            "tag" => $prev_tag,
            "in_tag" => $in_tag,
            "in_str" => $in_str,
            "offset" => $i + (int)$context['offset'],
            "str_char" => $str_char,
            "prev_char" => $prev_char
        );
        return true;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->_context;
    }



}