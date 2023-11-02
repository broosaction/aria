<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 8:05
 */

namespace Core\tpl\Handlers\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Contracts\TemplateCompiler;

class FindVariableInString implements \Core\tpl\Contracts\TemplateHandler
{

    /**
     * @inheritDoc
     */
    public function handle(&$content, AriaCompiler $compiler, $lang = 'PHP')
    {
        $var_match = array();
        preg_match_all('/\$([a-zA-Z0-9_\-\(\)\.\",>]+)/', $content, $var_match);
        if (!empty($var_match[0])) {
            foreach ($var_match[1] as $j => $var) {
                $_var_name = explode('.', $content);
                if (count($_var_name) > 1) {
                    $vn = $_var_name[0];
                    unset($_var_name[0]);
                    $mod = array();
                    foreach ($_var_name as $k => $index) {
                        $index = explode('->', $index, 2);
                        $obj = '';
                        if (count($index) > 1) {
                            $obj = '->' . $index[1];
                            $index = $index[0];
                        } else {
                            $index = $index[0];
                        }
                        if ($index[strlen($index) - 1] === ")") {
                            $mod[] = $index . $obj;
                        } else {
                            $vn .= "['$index']$obj";
                        }
                    }
                    $_var_name = '$' . $vn;
                    (new ApplyModifiersHandler($compiler))->applyModifiers($_var_name, $mod);

                } else {
                    $_var_name = '$' . $_var_name[0];
                }
                $content = str_replace(@$var_match[0][$j], '".' . $_var_name . '."', $content);
            }
        }
        return $var_match;
    }
}