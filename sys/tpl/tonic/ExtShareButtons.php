<?php


namespace Core\tpl\tonic;



class ExtShareButtons
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



        $this->tonic::extendModifier('shareontwitter', static function ($input, $val){

            if(!isset($val) || !isset($input)){
                throw new \Exception('value must not be empty');
            }

            if(!is_string($val) || !is_string($input)){
                throw new \Exception('value must be a String');
            }

            $l = $input ?? $val;
            echo '<a target="_blank" class="image-button icon-right" style="top:15px;" href="https://twitter.com/home?status='.$l.'">
                                                <span class="blue white-text mif-twitter icon"></span>
                                                <span class="caption">Share it</span>
                                            </a>';

        });

        $this->tonic::extendModifier('shareonfacebook', static function ($input, $val){

            if(!isset($val) || !isset($input)){
                throw new \Exception('value must not be empty');
            }

            if(!is_string($val) || !is_string($input)){
                throw new \Exception('value must be a String');
            }

            $l = $input ?? $val;
            echo '<a target="_blank" class="image-button icon-right" style="top:15px;" href="https://www.facebook.com/share.php?u='.$l.'">
                                                <span class="right bg-darkBlue white-text mif-facebook icon"></span>
                                                <span class="caption">Share it</span>
                                            </a>';

        });

        $this->tonic::extendModifier('shareonwhatsapp', static function ($input, $val){

            if(!isset($val) || !isset($input)){
                throw new \Exception('value must not be empty');
            }

            if(!is_string($val) || !is_string($input)){
                throw new \Exception('value must be a String');
            }

            $l = $input ?? $val;
            echo '<a target="_blank" class="image-button icon-right" style="top:15px;" 
            href="whatsapp://send?text=Hello check this out! '.$l.' this made my day, find time and view it '.$l.'" data-action="share/whatsapp/share">
                                                <span class="bg-teal white-text fa fa-whatsapp icon"></span>
                                                <span class="caption">Share it</span>
                                            </a>';

        });


    }

}