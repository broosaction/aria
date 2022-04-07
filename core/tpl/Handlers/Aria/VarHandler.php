<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 7:27
 */

namespace Core\tpl\Handlers\Aria;


use Core\Config\Config;
use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Contracts\TemplateHandler;
use Core\tpl\Support\Utils;

class VarHandler implements TemplateHandler
{

    /**
     * @inheritDoc
     */
    public function handle(&$content, AriaCompiler $compiler, $lang = 'PHP')
    {
        $matches = array();
        preg_match_all('/\{\s*\$(.+?)\s*\}/', $content, $matches);
        if (!empty($matches)) {
            foreach ($matches[1] as $i => $var_name) {
                $real_var = $var_name;

                $var_name = Utils::escapeCharsInString($var_name, '.', '**dot**');
                $var_name = explode('.', $var_name);
                if (count($var_name) > 1) {
                    $vn = $var_name[0];
                    if (empty($vn)) {
                        $vn = "__func";
                    }
                    unset($var_name[0]);
                    $mod = array();
                    foreach ($var_name as $j => $index) {
                        $index = str_replace('**dot**', '.', $index);
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
                            if (strpos($index, '$') === 0) {
                                $vn .= "[$index]$obj";
                            } else {
                                $vn .= "['$index']$obj";
                            }
                        }
                    }
                    $var_name = '$' . $vn;

                    (new ApplyModifiersHandler($compiler))->applyModifiers($var_name, $mod, $matches[0][$i]);
                } else {
                    $var_name = '$' . $var_name[0];
                    (new ApplyModifiersHandler($compiler))->applyModifiers($var_name, array(), $matches[0][$i]);

                }
                $rep = '<?php try{ echo @' . $var_name . '; } catch(\Exception $e) { echo $e->getMessage(); } ?>';
                $co = new Config();
                if (strpos($real_var, 'app_url') !== false) {

                    $content = Utils::str_replace_first($matches[0][$i], $co->app_url, $content);
                } else if (strpos($real_var, 'theme_url') !== false) {

                    $content = Utils::str_replace_first($matches[0][$i], $co->getThemeUrl(), $content);
                } else {
                    $content = Utils::str_replace_first($matches[0][$i], $rep, $content);
                }

            }
        }
    }
}