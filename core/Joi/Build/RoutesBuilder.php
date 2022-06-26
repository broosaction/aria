<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 31 /May, 2021 @ 23:00
 */

namespace Core\Joi\Build;





use Core\config\Config;
use Core\Joi\Build\Helpers\Properties;
use Core\Joi\Start;
use Nette\PhpGenerator\PhpNamespace;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use App\Bootstrap\Router;
use ReflectionClass;

class RoutesBuilder extends Builder
{

    private $classes = [];
    private $counter = 0;
    private $kownProperties = ['domain', 'prefix','middleware', 'exceptionHandler'];

    private Start $server;


    public function build()
    {

        foreach (Finder::findFiles('*.php', '*.properties')
                     ->from($this->server->server_home.'/app/Bootstrap/Controllers') as $file) {

            $this->prepare($file);

        }

        $this->output->writeln('found '.count($this->classes).' possible routes groups');
        $code = '';
        $namespace = new PhpNamespace('App\\Bootstrap\\Routes');
        $namespace->addUse(Router::class);
        $this->output->writeln('Building file');
        $code .= "\n Router::group(['namespace' => 'App\Bootstrap\Controllers'], static function () { \n\n";

        foreach ($this->classes as $key => $value){
            $_classes = $value['classes'];
            if(isset($_classes)){

                $code .= " Router::group( ".$this->buildProperties($value)." static function () {\n";
                $code .= "//**  ".$key." route group\n";
                $this->buildRoutes($_classes, $code);
                $code .= " });\n\n";

            }
        }

        $code .= "\n }); ";

       $cnt =  "<?php \n" . $namespace ." ".$code;
       FileSystem::write($this->server->server_home.'/App/Bootstrap/Routes/web.php', $cnt);
        $this->output->writeln('built file with '.$this->counter.' routes');
    }


    /**
     * @param $_classes
     * @param $code
     * @throws \ReflectionException
     */
    private function buildRoutes($_classes, &$code): void
    {
        foreach ($_classes as $k => $v){

            $cl = new ReflectionClass($v);
            foreach ($cl->getMethods() as $method){
                $props = new Properties($method->getDocComment());

                if($props->getMethod() !== null){
                    $config = new Config();
                    if( isset($config->app_folder) && $config->app_folder !== ''){
                        $url = '/'.$config->app_folder.$props->getPath();
                    }else{
                        $url = $props->getPath();
                    }
                    $v = str_replace('App\Bootstrap\Controllers\\','', $v);
                    $code .= "\t\t Router::match(".$props->getMethod().", '".$url."', '".$v."@".$method->getName()."', ".$props->getRouteProps().")->setName('".$props->getName()."'); \n";
                    $this->output->writeln('Added: '.$method->getName().'  listening on: '.$props->getMethod().$url);
                    $this->counter++;
                }

            }
        }
    }

    /**
     * @param $_file
     */

    private function prepare($_file){
        $file = str_replace($this->server->server_home.'/app/Bootstrap/','App\Bootstrap\\',$_file);

        $tree = explode('\\',$file);
        if(str_contains($file,'.php')){
            $class = str_replace('.php','',$file);

            if(count($tree)>2){
                $this->classes[$tree[count($tree)-2]]['classes'][] = $class;
            }else{
                $this->classes['index']['classes'][] = $class;
            }

        }else {
           // if the file is not PHP then its a properties file
           $this->prepareProperties($file, $_file, $tree);

        }

    }

    private function prepareProperties($file, $_file, $tree){
        if(str_contains($file,'.properties')){
            $component = FileSystem::read($_file);
            if($component !== ''){
                $lines = explode("\n", $component);
                foreach($lines as $line){
                    $line = str_replace(['\n', '\r',' '],'',$line);
                    $line = explode('=', $line);
                    if(in_array($line[0], $this->kownProperties, true) && $line[1] !== null && $line[1] !== ''){
                        //need a verifier to double check if the object really exists
                        $this->classes[$tree[count($tree)-2]][$line[0]] = $line[1] ?? '';
                    }
                }
            }
        }
    }


    private function buildProperties($class){
        $prop = '[';
        if( isset($class['middleware']) && $class['middleware'] !== ''){
            $prop .= $this->prepareMiddleware($class['middleware']).',';
        }
        if(isset($class['prefix'])  && $class['prefix'] !== ''){
            $prop .= $this->preparePrefix($class['prefix']).',';
        }
        if(isset($class['exceptionHandler']) && $class['exceptionHandler'] !== ''){
            $prop .= $this->prepareExceptionHandler($class['exceptionHandler']).',';
        }

        if(isset($class['domain'])){
            $prop .= $this->prepareDomain($class['domain']).'';
        }

        return $prop.'],';
    }

    private function prepareMiddleware($middleware){
        if($middleware !== ''){
            print_r($middleware);
            return "'middleware' => ".$middleware."::class";
        }
        return '';
    }

    private function preparePrefix($prefix){
        if($prefix !== ''){
            return "'prefix' => '".$prefix."'";
        }
        return '';
    }

    private function prepareExceptionHandler($exceptionHandler){
        if($exceptionHandler !== ''){
            return "'exceptionHandler' => ".$exceptionHandler."::class";
        }
        return '';
    }

    private function prepareDomain($domain){
        if($domain !== ''){
          $domain = Properties::createDomain($domain);
            return "'domain' => '".$domain."'";
        }
        return '';
    }

    /**
     * @param Start $server
     */
    public function setServer(Start $server): RoutesBuilder
    {
        $this->server = $server;

        return $this;
    }



}
