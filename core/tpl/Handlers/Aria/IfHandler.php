<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 8:12
 */

namespace Core\tpl\Handlers\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Contracts\TemplateCompiler;
use Core\tpl\Contracts\TemplateHandler;
use Core\tpl\Support\Utils;

class IfHandler implements TemplateHandler
{


    public function handle(&$content, AriaCompiler $compiler, $lang = 'PHP')
    {

        $matches = array();
        preg_match_all('/\{\s*(if|elseif)\s*(.+?)\s*\}/', $content, $matches);
        if (!empty($matches)) {
            foreach ($matches[2] as $i => $condition) {
                $condition = trim($condition);
                $condition = str_replace(array(
                    'eq',
                    'gt',
                    'lt',
                    'neq',
                    'or',
                    'gte',
                    'lte'
                ), array(
                    '==',
                    '>',
                    '<',
                    '!=',
                    '||',
                    '>=',
                    '<='
                ), $condition);
                $var_match = array();
                preg_match_all('/\$([a-zA-Z0-9_\-\(\)\.]+)/', $condition, $var_match);
                if (!empty($var_match)) {
                    foreach ($var_match[1] as $j => $var) {
                        $var_name = explode('.', $var);
                        if (count($var_name) > 1) {
                            $vn = $var_name[0];
                            unset($var_name[0]);
                            $mod = array();
                            foreach ($var_name as $k => $index) {
                                $index = explode('->', $index, 2);
                                $obj = '';
                                if (count($index) > 1) {
                                    $obj = '->' . $index[1];
                                    $index = $index[0];
                                } else {
                                    $index = $index[0];
                                }
                                if ($index[strlen($index) - 1] === ')') {
                                    $mod[] = $index . $obj;
                                } else {
                                    $vn .= "['$index']$obj";
                                }
                            }
                            $var_name = '$' . $vn;
                            (new ApplyModifiersHandler($compiler))->applyModifiers($var_name, $mod);

                        } else {
                            $var_name = '$' . $var_name[0];
                        }
                        $condition = str_replace(@$var_match[0][$j], $var_name, $condition);
                    }
                }
                $rep = '<?php ' . $matches[1][$i] . '(@' . $condition . '): ?>';
                $content = str_replace($matches[0][$i], $rep, $content);
            }
        }
        $content = preg_replace('/\{\s*(\/if|endif)\s*\}/', '<?php endif; ?>', $content);
        $content = preg_replace('/\{\s*else\s*\}/', '<?php else: ?>', $content);

    }

    public function handleIfMacros(&$content)
    {
        $match = Utils::matchTags('/<([a-xA-Z_\-0-9]+).+?a:if\s*=\s*"(.+?)".*?>/', '{endif}');
        if (empty($match)) {
            return false;
        }
        $content = preg_replace('/<([a-xA-Z_\-0-9]+)(.+?)a:if\s*=\s*"(.+?)"(.*?)>/', '{if $3}<$1$2$4>', $content);
        return true;
    }


}