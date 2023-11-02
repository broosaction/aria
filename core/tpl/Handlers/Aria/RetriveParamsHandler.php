<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 3:53
 */

namespace Core\tpl\Handlers\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Contracts\TemplateCompiler;
use Core\tpl\Contracts\TemplateHandler;

class RetriveParamsHandler implements TemplateHandler
{

    public function handle(&$content, AriaCompiler $compiler, $lang = 'PHP'): void
    {
        $i = 0;
        $p = [];
        $escaped = false;
        $in_str = false;
        $act = "";
        while ($i < strlen($content)) {
            $char = substr($content, $i, 1);
            $i++;
            switch ($char) {
                case "\\":
                    if ($escaped === true) {
                        $escaped = false;
                        $act .= $char;
                    } else {
                        $escaped = true;
                    }
                    break;
                case '"':
                    if ($escaped === true) {
                        $act .= $char;
                        break;
                    }
                    $in_str = $in_str === false;
                    break;
                case ',':
                    if ($in_str === true) {
                        $act .= $char;
                        break;
                    }
                    $p[] = $act;
                    $act = "";
                    break;
                default:
                    $escaped = false;
                    $act .= $char;
                    break;
            }
        }
        $p[] = $act;
        $content = $p;
    }
}