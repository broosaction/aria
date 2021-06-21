<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/11/2019
 * Time: 23:10
 */

namespace Core\joi;


use Nette\PhpGenerator\PhpNamespace;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Symfony\Component\Console\Output\OutputInterface;

class ConBuilder
{


    public static function readENV($dir,$file = '/config/config.io')
    {


        $namespace = new PhpNamespace('Core\\config');

        $class = $namespace->addClass('Config');

        $class->addComment('This is a Joi system auto generated class, Built on : '.date('Y-m-d H:i:s'));


        $class->addProperty('version', '1.1.0')
            ->setVisibility('public')
            ->setStatic()
            ->addComment('System version.');


        $contructor = $class->addMethod('__construct')

            ->setBody('');

          clearstatcache();
        $class->addProperty('last_modified', date('F d Y H:i:s.',filemtime($dir.$file)))
            ->setVisibility('public')
            ->addComment('config.io last modified date and time');

        $handle = fopen($dir.$file, 'r');
        if ($handle) {
            while (! feof($handle)) {
                $line = fgets($handle);
                // process the line read.
                if(stripos($line,'=')!==false) {
                    $line = strtolower($line);
                    $line = str_replace(' ','',$line);
                    $line = str_replace(array("\n", "\r"), '', $line);
                    $line = explode('=', $line);

                      $class->addProperty($line[0], $line[1].'')
                          ->setVisibility('public')
                          ->addComment($line[0]);
                  }
            }

            fclose($handle);
        } else {

        }


// $namespace->addUse('Bar\AliasedClass');



        $class->addProperty('items', [1, 2, 3])
            ->setVisibility('public')
            ->setStatic()
            ->addComment('@var int[]');

        $method = $class->addMethod('getThemeUrl')
            ->setVisibility('public')
            ->setBody('return $this->app_url."/themes/".$this->app_theme;')
             ->addComment('local and current  theme url. called in themes as {$.theme_url}');




        $config = fopen($dir.'/sys/config/Config.php', 'w');

        $configdata = "<?php \n" . $namespace ."\n ?>";
        fwrite($config, $configdata);
        fclose($config);

        return $namespace;

    }


    /** @noinspection BacktickOperatorUsageInspection */
    public static function buildControllers(OutputInterface $output, $dir, $folder = '/Controllers'){

        $namespace = new PhpNamespace('App\\Bootstrap');
        $namespace->addUse('Core\joi\Start');
        $namespace->addUse('Core\router\Web');

        $class = $namespace->addClass('Boot');
        $class->addComment('This is a Joi System auto generated class, Built on : '.date('Y-m-d H:i:s'));

        $class->addProperty('theme_dir','')
            ->setVisibility('public')->setPrivate()
            ->addComment('Themes directory.');

        $class->addProperty('theme_home','')
            ->setVisibility('public')->setPrivate()
            ->addComment('Themes directory home.');

        $class->addProperty('sys')->setType('Core\joi\Start')
            ->setVisibility('public')->setPrivate()
            ->addComment('System server');

        $output->writeln('Created class Boot');

        $method = $class->addMethod('__construct');
        $method->addComment('@return null'); // in comments resolve manually
        $method->addParameter('server')
            ->setType('Core\joi\Start'); // it will resolve to \Bar\OtherClass


         $code = '
          if(isset([d]server)){
            [d]this->sys =  [d]server;
        }else{
            [d]this->sys = new Start(__DIR__);
        }
        if(isset([d]this->sys->getConfig()->app_theme)) {
            [d]this->theme_dir = [d]this->sys->getServerHome() . \'/themes/\' . [d]this->sys->getConfig()->app_theme;

            [d]this->theme_home = [d]this->sys->getConfig()->app_url . \'/themes/\' . [d]this->sys->getConfig()->app_theme;
        }else{

            [d]this->theme_dir = [d]this->sys->getServerHome() . \'/themes/default\';

            [d]this->theme_home = [d]this->sys->getConfig()->app_url . \'/themes/default\';
        }


        [d]router = new Web();

        [d]tpl = [d]this->sys->getAria()->getTonic();

        [d]tpl->set_themes_dir([d]this->theme_dir);

        [d]cache = (new  \Core\drivers\Cache([d]server->getServerHome()))->getCacheEngine();

        [d]tpl::setGlobals();

        if(![d]this->sys->getConfig()->app_cache) {

            [d]tpl::[d]cache_dir = [d]this->theme_dir . \'/cache/\';

            [d]tpl::[d]enable_content_cache = true;
        }

         '.PHP_EOL;

        $code = str_replace('[d]','$',$code);

        $output->writeln('Starting to read the Controllers directory: '.$folder);
        foreach (Finder::findFiles('*.php')->from($dir.'/App'.$folder) as $file) {
            $output->writeln('Processing controller file @ '.$file);


            $inc = str_replace(array('.php',$dir),'',$file);

            $inc = str_replace('/',"\\",$inc);
            $inc .= '($router,$server,$tpl);';
            $code .= 'new '.$inc.PHP_EOL;
        }


        $code .= PHP_EOL.'$router->run();'.PHP_EOL;

       $method->addBody($code);

       $filen = $dir.'/app/Bootstrap/Boot.php';
       $dirn = dirname($filen);
       if(!is_dir($dirn)){
           if (!mkdir($dirn, 0755, true) && !is_dir($dirn)) {
               throw new \RuntimeException(sprintf('Directory "%s" was not created', $dirn));
           }
       }
        $config = fopen($dir.'/app/Bootstrap/Boot.php', 'w');

        $configdata = "<?php \n" . $namespace ."\n ";
        fwrite($config, $configdata);
        fclose($config);
        $output->writeln('Done, now recreating the Boot file.');
       return $code;
    }

}