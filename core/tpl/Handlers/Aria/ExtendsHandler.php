<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 10:28
 */

namespace Core\tpl\Handlers\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Contracts\TemplateCompiler;
use Core\tpl\Contracts\TemplateHandler;

class ExtendsHandler implements TemplateHandler
{

    public function handle(&$content, AriaCompiler $compiler, $lang = 'PHP')
    {
        $matches = array();
        preg_match_all('/\{\s*(extends )\s*(.+?)\s*\}/', $content, $matches);
        $base = $matches[2];
        if (count($base) <= 0) {
            return;
        }
        if (count($base) > 1) {
            throw new \Exception("Each template can extend 1 parent at the most");
        }
        $base = $base[0];
        if (strpos($base, '"') === 0) {
            $base = substr($base, 1);
        }
        if (substr($base, -1) === '"') {
            $base = substr($base, 0, -1);
        }
        $base = $compiler->getDir() . $base;
        if (!file_exists($base)) {
            throw new \Exception("Unable to extend base template " . $base);
        }
        //todo Add the extended content
        $content = str_replace($matches[0][0], "", $content);
    }
}