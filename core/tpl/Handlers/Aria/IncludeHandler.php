<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 2:47
 */

namespace Core\tpl\Handlers\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Contracts\TemplateCompiler;
use Core\tpl\Contracts\TemplateHandler;
use Nette\Utils\FileSystem;

class IncludeHandler implements TemplateHandler
{

    public function handle(&$content, AriaCompiler $compiler, $lang = 'PHP'): void
    {
        $matches = array();

        preg_match_all('/{\s*include\s*(.+?)\s*}/', $content, $matches);
        if (!empty($matches)) {
            foreach ($matches[1] as $i => $include) {
                $rep = '';
                $params = array();
                $include = $this->handleInclude($include, $params);
                if (strpos($include, 'http') === 0) {

                    $rep = file_get_contents($include);

                } else if ($compiler->getDir() !== null) {

                    $rep = FileSystem::read($compiler->getDir() . '/' . $compiler->buildPathName($include));
                    //we check for more includes
                    $this->handle($rep, $compiler);

                } else if ($compiler->getDir() === null) {

                    $this->handleObjectReCall($compiler->buildPathName($include));

                }

                $content = str_replace($matches[0][$i], $rep, $content);
            }
        }
    }

    private function handleInclude($include, &$params)
    {
        $include = trim($include);
        $include = explode(',', $include);

        if (count($include) > 1) {
            $inc = $include[0];
            unset($include[0]);
            foreach ($include as $kv) {
                @list($key, $val) = @explode('=', $kv);
                $params[$key] = empty($val) ? true : $val;
            }
            $include = $inc;
        } else {
            $include = $include[0];
        }
        return $include;
    }

    /**
     * @param $include
     * @return string
     */
    private function handleObjectReCall($include): string
    {
        /*
                   ob_start();
                   $inc = new Tonic($include);
                   $inc->setContext($this->assigned);
                   try {
                       $rep = $inc->render();
                   } catch (\Exception $e) {
                   }
                   $err = ob_get_clean();
                   if (!empty($err)) {
                       $rep = $err;
                   } */
        return '';
    }
}