<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 1:55
 */

namespace Core\tpl\Contracts;


use Core\tpl\Compilers\AriaCompiler;

interface TemplateHandler
{
    /**
     * @param $content
     * @param AriaCompiler $compiler
     * @param string $lang
     */
    public function handle(&$content,AriaCompiler $compiler, $lang = 'PHP');


}