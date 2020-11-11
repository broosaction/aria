<?php
/**
 * Created by Bruce Mubangwa on 11 /Nov, 2020 @ 16:08
 */

namespace App\Controllers;


use Core\joi\Start;
use Core\router\Web;
use Core\tpl\tonic\Tonic;

class HttpStatus
{

    public function __construct(Web $router, Start $server, Tonic $tpl)
    {
        $router->set404(function () {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            echo '404, route not found!';
        });
    }

}