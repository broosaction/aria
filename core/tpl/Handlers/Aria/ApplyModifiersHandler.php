<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 03 /Jun, 2021 @ 4:34
 */

namespace Core\tpl\Handlers\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Core\tpl\Compilers\BaseTemplateCompiler;
use Core\tpl\Contracts\TemplateHandler;

class ApplyModifiersHandler implements TemplateHandler
{

    private $compiler;
    private $content;

    /**
     * ApplyModifiersHandler constructor.
     * @param $compiler
     */
    public function __construct($compiler)
    {
        $this->compiler = $compiler;
    }


    /**
     * @inheritDoc
     */
    public function handle(&$content, AriaCompiler $compiler, $lang = '')
    {
       $this->content = $content;
       $this->compiler = $compiler;
       return $this;
    }

    public function applyModifiers(&$var,$mod,$match = ""){
        $context = null;
        $mods = $mod;
        if($this->compiler->getProperty(BaseTemplateCompiler::CONTEXT_AWARE) === true) {
            if(!empty($match) && !in_array("ignoreContext()", $mod, true)) {
                $vars = new GetVarContextHandler();
                $vars->handle($match, $this->compiler);
                $context = $vars->getContext();
                switch($context["tag"]){
                    default:
                        if($context['in_tag']){
                            $mod[] = 'contextTag(' . $context['in_str'] . ')';
                        } else {
                            $mod[] = 'contextOutTag()';
                        }
                        break;
                    case 'script':
                        $mod[] = 'contextJs(' . $context['in_str'] . ')';
                        break;
                }
            }
        }
        $this->compiler->setProperty(BaseTemplateCompiler::CUR_CONTEXT, $context);
        if(count($mod) <= 0){
            return;
        }
        $ov=$var;
        foreach($mod as $name){
            $modifier=explode('(',$name,2);
            $name=$modifier[0];
            $params=substr($modifier[1],0,-1);

            (new RetriveParamsHandler())->handle($params,$this->compiler);

            foreach($this->compiler->getExtensions() as $_name => $mods) {
                if($_name !== $name) {
                    continue;
                }

                $ov = '$this->compose("'.$_name.'",'.$ov.(!empty($params) ? ',"'.implode('","',$params).'"' : "").')';
            }

        }
        $var=$ov;
    }
}