<?php
/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/13/2019
 * Time: 21:35
 */

namespace Core\tpl\tonic;


use Core\joi\Start;

class Extensions
{

    private $tonic;
    private $server;

    /**
     * Extensions constructor.
     * @param $tonic
     */
    public function __construct(Tonic $tonic, Start $server)
    {
        $this->server = $server;
        if(!isset($tonic)){

            $tonic = new Tonic(); // dev level view leveler.
        }
        $this->tonic = $tonic;

        new ExtMath($this->tonic);
        new ExtGithub($this->tonic);
        new ExtCache($this->tonic, $this->server);
        new ExtDateTime($this->tonic);
        new ExtShareButtons($this->tonic);

        $this->extendTonic();
    }


    private function extendTonic()
    {
        $ses = $this->server->getSessions();
        $this->tonic::extendModifier('test', static function($input){
            return 'Aria works';
        });

        $this->tonic::extendModifier('sesid', static function ($input) use($ses){
            return $ses::getId();
        });

        $this->tonic::extendModifier('echo', static function ($input) use($ses){
            echo $input;
        });
        $this->tonic::extendModifier('stripplus', static function ($input) use($ses){
            echo str_replace('+',' ', $input);
        });
        $this->tonic::extendModifier('tochips', static function ($input) use($ses){
            $chips =  explode(',',$input);
            for($i = 0, $iMax = count($chips); $i <= $iMax-1; $i++){
                echo '<div class="chip">'.$chips[$i].'</div>';
            }
        });

        $this->tonic::extendModifier('selfurl', static function ($input) use($ses){

            $s = empty($_SERVER['HTTPS']) ? ''
                : ($_SERVER['HTTPS'] === 'on') ? "s"
                    : '';
            $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], '/') . $s));
            $port = ($_SERVER['SERVER_PORT'] === '80') ? ''
                : (':' .$_SERVER['SERVER_PORT']);
            return $protocol. '://' .$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];

        });

    }
}