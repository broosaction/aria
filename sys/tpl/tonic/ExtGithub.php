<?php


namespace Core\tpl\tonic;


use Github\Client;

class ExtGithub
{

    private $tonic;


    public function __construct(Tonic $tonic)
    {

        if(!isset($tonic)){

            $tonic = new Tonic(); // dev level view leveler..
        }

        $this->tonic = $tonic;

        $this->extend();
    }

    private function extend()
    {

        $client = new Client();

        $this->tonic::extendModifier('github_num_repo', static function ($input, $val) use($client){

            if(!isset($val) ){
                throw new \Exception('Github username must be set');
            }

            if(!is_string($val)){
                throw new \Exception('Github username must be a string');
            }


            try {
                $repos = $client->api('user')->repositories($val);
            }catch (\Exception $v){
                throw new \Exception('Github host lost');
            }

            return count($repos);
        });


    }
}