<?php
/**
 * Created by PhpStorm.
 * User: broos
 * Date: 4/20/2019
 * Time: 16:03
 */

namespace App\pages;






use Core\joi\Start;
use Core\router\Web;



class Route
{

   private $theme_dir = '';
   public $theme_home = '';
   public $sys;

    /**
     * route constructor.
     * @param $server
     */
    public function __construct($server)
    {
        if(isset($server)){
            $this->sys =  $server;
        }else{
            $this->sys = new Start(__DIR__);
        }

        $this->theme_dir = $this->sys->getServerHome().'/themes/'.$this->sys->getConfig()->app_theme;

        $this->theme_home = $this->sys->getConfig()->app_url.'/themes/'.$this->sys->getConfig()->app_theme;

        $router = new Web();

        $tpl = $this->sys->getAria()->getTonic();

        $tpl->set_themes_dir($this->theme_dir);



       $tpl::setGlobals();

        if(!$this->sys->getConfig()->app_cache) {

            $tpl::$cache_dir = $this->theme_dir . '/cache/';

            $tpl::$enable_content_cache = true;
        }

        $router->set404(function () {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            echo '404, route not found!';
        });



          //main page router
        $router->match('GET|POST','/g/{id}', function($id) use($tpl)  {
            $tpl->load("themes/default/demo.html");
            $tpl->my_date = date_create();

            $tpl->config = $this->sys->getConfig();
            echo $tpl->render();

            echo $id;
        });




        $router->match('GET|POST','/{id}', function($id) use($tpl)  {
            $tpl->load("themes/default/demo.html");
            $tpl->my_date = date_create();
            $tpl->num = 6;
            $tpl->nu = [2,3,4];
            $tpl->config = $this->sys->getConfig();
            echo $tpl->render();

            echo $id;
        });


        // without this the all site will be down
        //will run the main router.
        $router->run();


    }



    /**
     * @param $dir
     * @deprecated
     */
    public function set_themes_dir($dir)
    {
        $this->theme_dir = $dir;
    }




}