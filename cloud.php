<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 4/20/2019
 * Time: 04:25
 */

//include_once '../vendor/autoload.php';
use App\Controllers\Route;


include_once 'vendor/autoload.php';


use Tracy\Debugger;

//we start our Framework core functions
$server = new Core\joi\Start(__DIR__);

//Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');

/* call \Tracy\OutputDebugger::enable();
 *  if there is any error or out put before the main app
 */

Debugger::$showBar = true;
Debugger::$strictMode = true;
Debugger::enable(Debugger::DETECT);


/*
 * we load our app logic,
 * Boot is automatically creates and can be updated by running
 *        php aria build-controllers
 * in future versions is will be automatically detected if changes occur in the App\Controllers package
 */
$app = new \App\Bootstrap\Boot($server);
