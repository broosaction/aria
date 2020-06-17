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
                $line = explode('=', $line);
                    $line = str_replace(array("\n", "\r"), '', $line);


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


}