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



include_once 'vendor/autoload.php';


use App\Bootstrap\Router;
use Tracy\Debugger;


//Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');

/* call \Tracy\OutputDebugger::enable();
 *  if there is any error or out put before the main app
 */
//we start our Framework core functions
$server = new Core\joi\Start(__DIR__);

//start our debuggers
Debugger::$showBar = true;
Debugger::$strictMode = true;
Debugger::enable(Debugger::DETECT, __DIR__ . '/logs');

/*
 * we load our app logic,
 * Boot is automatically created and can be updated by running
 *        php aria build-controllers
 * in future versions is will be automatically detected if changes occur in the App\Bootstrap\Controllers package
 */
Router::start($server);