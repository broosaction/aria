<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 31 /May, 2021 @ 21:23
 */

namespace App\Bootstrap;



use App\Bootstrap\Events\EventAddRoute;
use App\Bootstrap\Events\EventAll;
use App\Bootstrap\Events\EventBoot;
use App\Bootstrap\Events\EventInit;
use App\Bootstrap\Events\EventLoad;
use App\Bootstrap\Events\EventRewrite;
use Core\joi\Start;
use Core\joi\System\Exceptions\HttpException;
use Core\joi\System\Exceptions\NotFoundHttpException;
use Core\joi\System\Exceptions\TokenMismatchException;
use Core\Router\Event\EventArgument;
use Core\Router\Handlers\EventHandler;
use Core\Router\AriaRouter;

class Router extends AriaRouter
{
    /**
     * @param Start $server
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws TokenMismatchException
     */
    public static function start(Start $server): void
    {
        // Load our helpers
        require_once 'Routes/helpers.php';

        // Load our custom routes
        require 'Routes/web.php';

        //disable multi route Rendering
        parent::enableMultiRouteRendering(false);

        // Add event that fires when a route is rendered
        $eventHandler = new EventHandler();

        //Fires when a event is triggered.
        $eventHandler->register(EventHandler::EVENT_ALL, static function(EventArgument $argument) use($server) {
            (new EventAll())->handle($server, $argument);
        });

        //Fires when router is initializing and before routes are loaded.
        $eventHandler->register(EventHandler::EVENT_INIT, static function(EventArgument $argument) use($server) {
            (new EventInit())->handle($server, $argument);
        });

        //Fires when all routes has been loaded and rendered, just before the output is returned.
        $eventHandler->register(EventHandler::EVENT_LOAD, static function(EventArgument $argument) use($server) {
            (new EventLoad())->handle($server, $argument);
        });

        //`route`<br>`isSubRoute` | Fires when route is added to the router.
        // `isSubRoute` is true when sub-route is rendered.
        $eventHandler->register(EventHandler::EVENT_ADD_ROUTE, static function(EventArgument $argument) use($server) {
            (new EventAddRoute())->handle($server, $argument);
        });

        // Fires when a url-rewrite is and just before the routes are re-initialized.
        $eventHandler->register(EventHandler::EVENT_REWRITE, static function(EventArgument $argument) use($server) {
            (new EventRewrite())->handle($server, $argument);
        });

        // Fires when the router is booting. This happens just
        // before boot-managers are rendered and before any routes has been loaded.
        $eventHandler->register(EventHandler::EVENT_BOOT, static function(EventArgument $argument) use($server) {
            (new EventBoot())->handle($server, $argument);
        });


        parent::addEventHandler($eventHandler);

        // Do initial stuff
        parent::start($server);
    }

}