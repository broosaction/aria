<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/13/2019
 * Time: 21:35
 */

namespace Core\tpl\Extensions\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Nette\Caching\Cache;


class Extensions
{


    private AriaCompiler $ariaCompiler;

    /**
     * Extensions constructor.
     * @param AriaCompiler $ariaCompiler
     */
    public function __construct(AriaCompiler $ariaCompiler)
    {

        $this->ariaCompiler = $ariaCompiler;
        (new ExtMath($ariaCompiler));
        (new ExtDateTime($ariaCompiler));


        $this->extendTonic();
    }


    private function extendTonic()
    {


        $this->ariaCompiler->composer('test', static function ($input) {
            return 'Aria works';
        });

        $this->ariaCompiler->composer('time', static function ($input) {
            return time();
        });



        $this->ariaCompiler->composer('echo', static function ($input) {
            echo $input;
        });

        $this->ariaCompiler->composer('base64', static function ($input) {
            echo base64_encode($input);
        });

        $this->ariaCompiler->composer('contains', static function ($input, $val) {
            return str_contains($input, $val);
        });

        $this->ariaCompiler->composer('stripplus', static function ($input) {
            echo str_replace('+', ' ', $input);
        });




        $this->ariaCompiler->composer('selfurl', static function ($input) {

            $s = (empty($_SERVER['HTTPS']) ? ''
                : ($_SERVER['HTTPS'] === 'on')) ? "s"
                : '';
            $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], '/') . $s));
            $port = ($_SERVER['SERVER_PORT'] === '80') ? ''
                : (':' . $_SERVER['SERVER_PORT']);
            return $protocol . '://' . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];

        });

    }
}