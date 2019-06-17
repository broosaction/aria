<?php
/**
 * Created by PhpStorm.
 * User: broos
 * Date: 4/20/2019
 * Time: 04:25
 */

if (file_exists(__DIR__.'../../vendor/autoload.php')) {
    require __DIR__.'../../vendor/autoload.php';
} else {
    require __DIR__.'/vendor/autoload.php';
}
use App\pages\Route;

$server = new Core\joi\Start(__DIR__);

$app = new Route($server);


//$test = \Core\joi\ConBuilder::readENV(__DIR__ ,'/config/config.io');


//echo $test;

//echo __DIR__;
