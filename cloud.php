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
include_once 'libs/Libraries.php';


use App\Bootstrap\Router;
use Tracy\Debugger;


$server = new Core\joi\Start(__DIR__);

//start our debuggers
Debugger::$showBar = true;
Debugger::$strictMode = true;
Debugger::enable(Debugger::DETECT, __DIR__ . '/logs');

Router::start($server);