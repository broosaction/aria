<?php
/*
 * Copyright (c) 2020.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Core\tpl\tonic;


use Core\joi\Start;


class ExtCache
{


    private $tonic;
    private $server;

    /**
     * Extensions constructor.
     * @param Tonic $tonic
     * @param Start $server
     */
    public function __construct(Tonic $tonic, Start $server)
    {
        $this->server = $server;
        if(!isset($tonic)){

            $tonic = new Tonic(); // dev level view leveler.
        }
        $this->tonic = $tonic;

        $this->extend();
    }

    private function extend()
    {

        $client =  $this->server->getCache();

        $this->tonic::extendModifier('cache_get', static function ($input, $val) use($client){

            if(!isset($val) ){
                throw new \Exception('key value must be set');
            }


            return $client->load($val);
        });


        $this->tonic::extendModifier('set_cache', static function ($input, $val, $exp = '') use($client){

            if(!isset($input) ){
                throw new \Exception('value must be set');
            }


            if(!isset($val) ){
                throw new \Exception('key must be set');
            }

            if($exp === ''){
               $exp = '3 days';
            }

            $expi = array(
                Cache::EXPIRE => $exp, // accepts also seconds or a timestamp.
            );

            return $client->save( $val, $input ,$expi);
        });


        $this->tonic::extendModifier('remove_cache', static function ($input, $val) use($client){

            if(!isset($val) ){
                throw new \Exception('key value must be set');
            }


            return $client->remove($val);
        });

    }

}