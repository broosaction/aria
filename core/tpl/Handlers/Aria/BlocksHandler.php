<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 10:35
 */

namespace Core\tpl\Handlers\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Contracts\TemplateCompiler;
use Core\tpl\Contracts\TemplateHandler;
use Core\tpl\Support\Utils;

class BlocksHandler implements TemplateHandler
{



    public function handle(&$content, AriaCompiler $compiler, $lang = 'PHP')
    {
        $matches=array();
        preg_match_all('/\{\s*(block)\s*(.+?)\s*\}/',$content,$matches);
        $blocks = $matches[2];
        if(count($blocks) <= 0) {
            return;
        }
        foreach($blocks as $i => $block) {
            $block = trim($block);
            $rv = '<?php ob_start(array(&$this, "ob_'.$block.'")); ?>';
            $content = str_replace($matches[0][$i], $rv, $content);
        }
        $content=preg_replace('/\{\s*endblock\s*\}/','<?php ob_end_flush(); ?>',$content);
    }

    public function handleBlockMacros(&$content){
        $match = Utils::matchTags('/<([a-xA-Z_\-0-9]+).+?a:block\s*=\s*"(.+?)".*?>/','{endblock}');
        if (empty($match)) {
            return false;
        }
        $content = preg_replace('/<([a-xA-Z_\-0-9]+)(.+?)a:block\s*=\s*"(.+?)"(.*?)>/','{block $3}<$1$2$4>',$content);
    }
}