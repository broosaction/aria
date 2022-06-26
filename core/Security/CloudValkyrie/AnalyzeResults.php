<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 06 /Jun, 2021 @ 21:19
 */

namespace Core\Security\CloudValkyrie;


use Core\Joi\Start;
use Core\Joi\System\Time;
use Core\Joi\System\Utils;
use Core\Security\Utils\ValkyrieUtils;
use Core\tpl\Aria;

class AnalyzeResults
{

    protected ScanResults $scanResult;
    private $resultsArray;



    public function analyze($resultsArray){

        $this->resultsArray = $resultsArray;

        $blockVotes = 0;

        foreach ($this->resultsArray as $result) {
            $this->scanResult = $result;
            if($this->scanResult->isThreat()){
                ++$blockVotes;
            }

        }

        if($blockVotes > 0){
            $this->blockPage();
        }
    }

    /**
     * block session Hijack
     */
    private  function blockPage(): void
    {

        if(ValkyrieConfig::$BLOCK_SCREEN){
            $view = new Aria();
            $view->setThemeDir(Start::$SERVERROOT.'/core/tpl/local');
            $view->threatcount = count($this->resultsArray);
            $view->time = new Time();
            $view->ip = Utils::get_ip_address().' '.ValkyrieUtils::get_user_country_code();
            $view->version = ValkyrieConfig::getVersion();
            $view->render('valkyrie_block_page');
            die();
        }
       //wow
    }

    /**
     * @param $message
     */
    public function _invoke_just_block($message): void
    {
        $view = new Aria();
        $view->setThemeDir(Start::$SERVERROOT.'/core/tpl/local');
        $view->threatcount = count($this->resultsArray);
        $view->time = new Time();
        $view->message = $message;
        $view->ip = Utils::get_ip_address().' '.ValkyrieUtils::get_user_country_code();
        $view->version = ValkyrieConfig::getVersion();
        $view->render('valkyrie_block_page');
        die();
    }

}