<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 01 /Jun, 2021 @ 9:28
 */

namespace App\Bootstrap\Handlers;


use Core\joi\Start;
use Core\joi\System\Exceptions\NotFoundHttpException;
use Core\Router\Handlers\IExceptionHandler;
use Core\Router\Http\Request;
use Exception;
use Tracy\ILogger;

class CustomExceptionHandler implements IExceptionHandler
{

    public function handleError(Start $server, Request $request, Exception $error): void
    {
        /* You can use the exception handler to format errors depending on the request and type. */

        if ($request->getUrl()->contains('/api')) {

            response()->json([
                'error' => $error->getMessage(),
                'code'  => $error->getCode(),
            ]);

        }

        /* The router will throw the NotFoundHttpException on 404 */
       else if($error instanceof NotFoundHttpException) {

            /*
             * Render your own custom 404-view, rewrite the request to another route,
             * or simply return the $request object to ignore the error and continue on rendering the route.
             *
             * The code below will make the router render our page.notfound route.
             */

           // $request->setRewriteCallback('Controllers\DefaultController@notFound');
           //or you can set a custome 404 view
           //or you can use Valkyrie interface to print out the message
            //aria()->getValkyrie()->invoke('<b>'.$error->getCode().'</b> '.$error->getMessage());
         //   return;
           var_dump($error);

        }
        else {
            aria()->getLog()->log($error,ILogger::ERROR);
            view()->config = aria()->getConfig();
            view()->render('maintenance', false);
        }

        throw $error;

    }

}