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

namespace Core\Joi;


use Nette\PhpGenerator\PhpNamespace;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Symfony\Component\Console\Output\OutputInterface;

class ConBuilder
{


    public static function readENV($dir,$file = '/config/config.io')
    {


        $namespace = new PhpNamespace('Core\\Config');

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

        $class->addMethod('getSessionLifeTime')
            ->setVisibility('public')
            ->setStatic()
            ->setBody('return 60 * 60 * 24;')
            ->addComment('');






        $configdata = "<?php \n" . $namespace ."\n ?>";

        FileSystem::write($dir.'/core/Config/Config.php',  $configdata);


        return $namespace;

    }


}