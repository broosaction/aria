<?php
/**
 * Created by PhpStorm.
 * User: broos
 * Date: 4/20/2019
 * Time: 00:51
 */

namespace Core\config;
use Nette\PhpGenerator;

class Config
{
    public static function hi(){
        return 'works';
    }


    public static function readENV($dir,$file = '/config/config.io'){


        $handle = fopen($dir.$file, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // process the line read.
                $line = str_replace(' ','',$line);
                $line = explode('=', $line);

            }

            fclose($handle);
        } else {
            // error opening the file.
        }


// $namespace->addUse('Bar\AliasedClass');

        $namespace = new PhpGenerator\PhpNamespace('Env');

        $class = $namespace->addClass('Sys_Var');

        $class->addComment('This is a Joi system auto generated class, Built on : '.date('Y-m-d H:i:s'));

        $class->addConstant('version', '1.1.0')
            ->setVisibility('public'); // constant visiblity

        $class->addProperty('items', [1, 2, 3])
            ->setVisibility('public')
            ->setStatic()
            ->addComment('@var int[]');

        $method = $class->addMethod('getValue')
            ->setReturnType('int') // method return type
            ->setReturnNullable() // nullable return type
            ->setBody('return count($this->items);');

        $method->addParameter('id')
            ->setTypeHint('int') // scalar type hint
            ->setNullable(); // nullable type hint

        return $namespace;

    }

}