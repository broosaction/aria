<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 01 /Jun, 2021 @ 3:25
 */

namespace Core\Joi\Build\Helpers;


use Core\Config\Config;
use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\Set\PhpDocumentor;
use Jasny\PhpdocParser\Tag\CustomTag;
use Jasny\PhpdocParser\Tag\DescriptionTag;
use Jasny\PhpdocParser\Tag\FlagTag;
use Jasny\PhpdocParser\Tag\WordTag;
use Nette\Utils\Strings;

class Properties
{

    /**
     * @var
     */
    private $doc;

    /**
     * @var
     */
    private $annotations;

    /**
     * Properties constructor.
     * @param $doc
     */
    public function __construct($doc)
    {
        $this->doc = $doc;
        $this->getProperties();
    }

    private function getProperties()
    {
        $customTags = [
            new FlagTag('important'),
            new DescriptionTag('method'),
            new WordTag('path'),
            new WordTag('id'),
            new WordTag('middleware'),
            new WordTag('defaultParameterRegex'),
            new DescriptionTag('where'),
            new DescriptionTag('as')
        ];
        $tags = PhpDocumentor::tags()->with($customTags);

        $parser = new PHPDocParser($tags);
        $this->annotations = $parser->parse($this->doc);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * @param $tag
     * @return |null
     */
    public function getTag($tag)
    {
        return $this->annotations[$tag] ?? null;
    }

    /**
     * @return |null
     */
    public function getMethod()
    {
        return $this->annotations['method'] ?? null;
    }

    /**
     * @return |null
     */
    public function getPath()
    {
        return $this->annotations['path'] ?? null;
    }

    /**
     * @return |null
     */
    public function getName()
    {
        return $this->annotations['id'] ?? null;
    }

    /**
     * @return |null
     */
    public function getMiddleWare()
    {
        return $this->annotations['middleware'] ?? null;
    }

    public function getRouteProps()
    {
        $prop = '[';
        if (isset($this->annotations['defaultParameterRegex']) && $this->annotations['defaultParameterRegex'] !== '') {
            $prop .= "'defaultParameterRegex' => '" . $this->annotations['defaultParameterRegex'] . "',";
        }
        if (isset($this->annotations['where']) && $this->annotations['where'] !== '') {
            $prop .= "'where' => " . $this->annotations['where'] . ",";
        }
        if (isset($this->annotations['as']) && $this->annotations['as'] !== '') {
            $prop .= "'as' => '" . $this->annotations['as'] . "',";
        }
        if (isset($this->annotations['middleware']) && $this->annotations['middleware'] !== '') {
            $prop .= "'middleware' => " . $this->annotations['middleware'] . "::class,";
        }
        return $prop . '],';
    }

    public static function createDomain($domain)
    {
        $domain = Strings::lower($domain);
        return str_replace('domain', Config::$app_domain, $domain);
    }

}