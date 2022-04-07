<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Core\tpl\Extensions\Aria;


use Core\tpl\Compilers\AriaCompiler;
use Github\Client;
use Github\Exception\ErrorException;

class ExtGithub
{

    private AriaCompiler $ariaCompiler;

    /**
     * Extensions constructor.
     * @param AriaCompiler $ariaCompiler
     */
    public function __construct(AriaCompiler $ariaCompiler)
    {
        $this->ariaCompiler = $ariaCompiler;
        $this->extend();
    }


    private function extend()
    {


        $client = new \Github\Client();

        $this->ariaCompiler->composer('github_num_repo', static function ($input, $val) use ($client) {

            if (!isset($val)) {
                throw new \Exception('Github username must be set');
            }

            if (!is_string($val)) {
                throw new \Exception('Github username must be a string');
            }


            try {
                $repos = $client->api('user')->repositories($val) ?? 0;
            } catch (ErrorException $v) {
                throw new \Exception('Github host null');
            } catch (\TypeError $t) {
                throw new \Exception('type null');
            } catch (\Exception $v) {
                throw new \Exception('Github host lost');
            }

            return count($repos);
        });


    }
}