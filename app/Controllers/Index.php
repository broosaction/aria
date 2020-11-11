<?php
/**
 * Created by Bruce Mubangwa on 11 /Nov, 2020 @ 16:06
 */

namespace App\Controllers;


use Core\joi\Start;
use Core\router\Web;
use Core\tpl\tonic\Tonic;

class Index
{

    /**
     * Index constructor.
     *  it is the entry point, more like a group for the routes
     * @param Web $router
     * @param Start $server
     * @param Tonic $tpl
     */
    public function __construct(Web $router, Start $server, Tonic $tpl)
    {

        $router->match('GET|POST', '/{id}', function ($id) use ($tpl, $server) {

            //the default templating engine is Tonic

            //in tonic you can load your html or aria files in your themes directory
            //in future versions, it will be optionally adding the exact resource path: i.e theme name
            $tpl->load("themes/default/index.aria");

            //you can add objects to be used in the targeted html or aria file
            $tpl->my_date = date_create();

            //numbers, strings or arrays can be pursed too
            $tpl->version = $server->getConfig()::$version;
            $tpl->nu = [2, 3, 4];

            $tpl->config = $server->getConfig();

            // lastly we echo out our rendered html or aria file from themes
            echo $tpl->render();

        });
    }

}