<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 10:38
 */

namespace Core\tpl\Handlers\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Contracts\TemplateCompiler;
use Core\tpl\Contracts\TemplateHandler;
use Core\tpl\Support\Utils;

class HandleVar implements TemplateHandler
{

    public function handle(&$content, AriaCompiler $compiler, $lang = 'PHP')
    {
        $matches = [];
        preg_match_all('/\{\s*(var|let|set)\s*(.+?)\s*\}/', $content, $matches);
        if (!empty($matches)) {
            foreach ($matches[2] as $i => $var) {

                $var = str_replace(' = ', '**in**', $var);

                $var_det = explode('**in**', $var);

                if (is_array($var_det)) {
                    $key = $var_det[0];
                    $key = Utils::removeWhiteSpaces($key);
                    $key = str_replace('$', '', $key);
                    $val = $var_det[1];
                }
                $compiler->assign($key, $val);
                $rep = '<?php $' . $key . ' = ' . $val . '; ?>';
                $content = str_replace($matches[0][$i], $rep, $content);
            }
        }
    }
}