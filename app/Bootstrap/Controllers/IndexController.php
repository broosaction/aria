<?php


namespace App\Bootstrap\Controllers;


use Core\Router\Controllers\BaseController;

class IndexController extends BaseController
{

    /**
     * This comment contains important information that instructs the system when
     * building the Controller Link path. All Controller meta-data are in comments.
     * @method ['get','post']
     * @path /
     * @id index
     */
    public function index()
    {

        //view binds the data to the template
        view()->my_date = date_create();

        //numbers, strings or arrays can be pursed too
        view()->version = 2.0;
        view()->nu = [2, 3, 4];

        view()->config = aria()->getConfig();
        //we render our view, we can ch
        view()->render('index', false);

    }


}