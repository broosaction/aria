<?php 
namespace App\Bootstrap\Routes;

use App\Bootstrap\Router;

 
 Router::group(['namespace' => 'App\Bootstrap\Controllers'], static function () { 

 Router::group( ['middleware' => \App\Bootstrap\Middlewares\IndexMonitor::class,], static function () {
//**  Controllers route group
		 Router::match(['get','post'], '/', 'IndexController@index', [],)->setName('index'); 
 });

 Router::group( ['middleware' => \App\Bootstrap\Middlewares\ApiVerification::class,'exceptionHandler' => \App\Bootstrap\Handlers\CustomExceptionHandler::class,], static function () {
//**  API route group
		 Router::match(['get', 'post'], '/api/{key}', 'API\TestApi@api', ['defaultParameterRegex' => '.*?',],)->setName('Api'); 
 });

 Router::group( [], static function () {
//**  Files route group
		 Router::match(['get', 'post'], '/ads.txt', 'Files\Ads@adsTxt', [],)->setName('adsTxt'); 
		 Router::match(['get', 'post'], '/cdn/js/aria/{id?}', 'Files\DivAjaxMapping@ajaxMapping', ['defaultParameterRegex' => '.*?',],)->setName('ajaxMapping'); 
		 Router::match(['get', 'post'], '/cdn/js/joi', 'Files\JoiButton@JoiJsButton', [],)->setName('joiButton'); 
		 Router::match(['get', 'post'], '/site.webmanifest', 'Files\WebManifest@siteWebmanifest', [],)->setName('siteWebmanifest'); 
 });

 Router::group( [], static function () {
//**  Uptime route group
		 Router::match(['get', 'post'], '/uptime/check.app', 'Files\Uptime\Check@upTimeCheckApp', [],)->setName('UptimeCheck'); 
 });


 }); 