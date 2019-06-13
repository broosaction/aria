<?php
/**
 * Created by PhpStorm.
 * User: broos
 * Date: 4/20/2019
 * Time: 04:25
 */

include_once '../vendor/autoload.php';


$server = new Core\joi\Start(__DIR__);

$app = new \App\pages\Route($server);


//$test = \Core\joi\ConBuilder::readENV(__DIR__ ,'/config/config.io');


//echo $test;

//echo __DIR__;