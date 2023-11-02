<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 10:46
 */

namespace Core\tpl\Handlers\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Contracts\TemplateCompiler;
use Core\tpl\Contracts\TemplateHandler;
use Core\tpl\Support\Utils;

class HandleLoops implements TemplateHandler
{


    public function handle(&$content, AriaCompiler $compiler, $lang = 'PHP')
    {
        $matches = array();
        preg_match_all('/\{\s*(loop|for)\s*(.+?)\s*\}/', $content, $matches);
        if (!empty($matches)) {
            foreach ($matches[2] as $i => $loop) {
                $loop = str_replace(' in ', '**in**', $loop);
                $loop = Utils::removeWhiteSpaces($loop);
                $loop_det = explode('**in**', $loop);
                $loop_name = $loop_det[1];
                unset($loop_det[1]);
                $loop_name = explode('.', $loop_name);
                if (count($loop_name) > 1) {
                    $ln = $loop_name[0];
                    unset($loop_name[0]);
                    foreach ($loop_name as $j => $suffix) {
                        $ln .= "['$suffix']";
                    }
                    $loop_name = $ln;
                } else {
                    $loop_name = $loop_name[0];
                }
                $key = NULL;
                $val = NULL;
                $loop_vars = explode(',', $loop_det[0]);
                if (count($loop_vars) > 1) {
                    $key = $loop_vars[0];
                    $val = $loop_vars[1];
                } else {
                    $val = $loop_vars[0];
                }
                foreach ($loop_det as $j => $_val) {
                    @list($k, $v) = explode(',', $_val);
                    if ($k === 'key') {
                        $key = $v;
                        continue;
                    }
                    if ($k === 'item') {
                        $val = $v;
                        // continue;
                    }
                }
                $rep = '<?php foreach(' . $loop_name . ' as ' . (!empty($key) ? $key . ' => ' . $val : ' ' . $val) . '): ?>';
                $content = str_replace($matches[0][$i], $rep, $content);
            }
        }
        $content = preg_replace('/\{\s*(\/loop|endloop|\/for|endfor)\s*\}/', '<?php endforeach; ?>', $content);
    }

    public function handleLoopMacros(&$content)
    {
        $match = Utils::matchTags('/<([a-xA-Z_\-0-9]+).+?a:loop\s*=\s*"(.+?)".*?>/', '{endloop}');
        if (empty($match)) {
            return false;
        }
        $content = preg_replace('/<([a-xA-Z_\-0-9]+)(.+?)a:loop\s*=\s*"(.+?)"(.*?)>/', '{loop $3}<$1$2$4>', $content);
    }
}