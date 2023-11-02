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

namespace Core\Joi\Build;


use Core\Security\SecureRandom;
use Dotenv\Dotenv;
use Nette\PhpGenerator\PhpNamespace;
use Nette\Utils\Strings;

class ConBuilder
{


    public static function readENV()
    {

        $namespace = new PhpNamespace('Core\\Config');

        $class = $namespace->addClass('Config');

        $class->addComment('This is a Joi system auto generated class, Built on : ' . date('Y-m-d H:i:s'));


        $class->addProperty('version', '1.1.0')
            ->setVisibility('public')
            ->setStatic()
            ->addComment('System version.');

        $class->addProperty('parameters', [])
            ->setVisibility('protected')
            ->addComment('');


        $contructor = $class->addMethod('__construct')
            ->setBody('');

        clearstatcache();
        $class->addProperty('last_modified', date('F d Y H:i:s.'))
            ->setVisibility('public')
            ->addComment('config.io last modified date and time');


        foreach ($_ENV as $key => $item):

            if (Strings::lower($key) === 'app_key') {
                $item = (new SecureRandom())->generate(64);
            }

            $class->addProperty(Strings::lower($key), $item)
                ->setVisibility('public')
                ->addComment($key);

        endforeach;

        $method = $class->addMethod('getThemeUrl')
            ->setVisibility('public')
            ->setBody('return $this->app_url."/themes/".$this->app_theme;')
            ->addComment('local and current  theme url. called in themes as {$.theme_url}');

        $class->addMethod('getSessionLifeTime')
            ->setVisibility('public')
            ->setStatic()
            ->setBody('return 60 * 60 * 24;');

        $method = $class->addMethod('get')
            ->setVisibility('public')
            ->addAttribute('key')
            ->setBody('return $this->parameters[$key] ?? $defaultValue;');
        $method->addParameter('key')->setType('string');
        $method->addParameter('defaultValue')->setType('string')->setDefaultValue(null);


        $method = $class->addMethod('__set')
            ->setVisibility('public')
            ->addAttribute('name')
            ->setBody('$this->parameters[$name] = $value;')
            ->addComment('');
        $method->addParameter('name');
        $method->addParameter('value');

        $method = $class->addMethod('__get')
            ->setVisibility('public')
            ->addAttribute('name')
            ->setBody('return $this->parameters[$name] ?? null;');
        $method->addParameter('name');

        $method = $class->addMethod('__isset')
            ->setVisibility('public')
            ->addAttribute('name')
            ->setBody('return array_key_exists($name, $this->parameters);');
        $method->addParameter('name');


        $class->addMethod('getEncryptionMethod')
            ->setVisibility('public')
            ->addAttribute('name')
            ->setBody('return "AES-256-CBC";');


        return "<?php \n" . $namespace;

    }


}