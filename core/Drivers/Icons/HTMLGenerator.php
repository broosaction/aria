<?php

namespace Core\Drivers\Icons;

use Core\Joi\System\Stubs\FaviconGenerator;

class HTMLGenerator extends FaviconGenerator
{

    private $msapplicationTileColor;
    private $themeColor;
    private $titleBarColor;



    public function __construct($applicationName = 'Site Name', $faviconDir = 'assets/icons/', $msapplicationTileColor = '#FFF', $themeColor = '#FFF', $titleBarColor = '#FFF')
    {
        parent::__construct($applicationName, $faviconDir);
        $this->applicationName = $applicationName;
        $this->msapplicationTileColor = $msapplicationTileColor;
        $this->themecolor = $themeColor;
        $this->titlebarColor = $titleBarColor;
    }

    public function generateMeta()
    {
        $html = '';
        $html .= '<meta name="application-name" content="'.$this->applicationName.'" />';
        $html .= '<meta name="apple-mobile-web-app-capable" content="yes" />';
        $html .= '<meta name="apple-mobile-web-app-title" content="'.$this->applicationName.'" />';
        $html .= '<meta name="msapplication-TileColor" content="'.$this->msapplicationTileColor.'" />';
        $html .= '<meta name="theme-color" content="'.$this->themecolor.'" />';
        $html .= '<meta name="apple-mobile-web-app-status-bar-style" content="'.$this->titlebarColor.'" />';
        return $html;
    }

    public function generate()
    {
        $html = '';
        $html .= $this->generateMeta();
        $html .= $this->generateApple();
        $html .= $this->generateAndroid();
        $html .= $this->generateMs();
        $html .= $this->generateStartupScreens();
        $html .= $this->generateLinks();
        return $html;
    }

    public function generateApple()
    {
        $html = '';
        foreach ($this->apple as $ap) {
            $path = $this->faviconDir;
            $width = $ap['width'];
            $height = $ap['height'];
            $fileName = $ap['name'].'-'.$width.'x'.$height.'.'.$ap['ext'];
            $sizes = $width.'x'.$height;
            $html .= '<link rel="'.$ap['rel'].'" sizes="'.$sizes.'" href="'.$path.$fileName.$this->v.'" />';
        }
        return $html;
    }

    public function generateStartupScreens()
    {
        $html = '';
        foreach ($this->appleStartupScreens as $aSS) {
            $width = $aSS['width'];
            $height = $aSS['height'];
            $fileName = $aSS['name'].'-'.$width.'x'.$height.'.'.$aSS['ext'];
            $path = $this->faviconDir;
            $html .= '<link href="'.$path.$fileName.$this->v.'" media="'.$aSS['media'].'" rel="'.$aSS['rel'].'" />';
        }
        return $html;
    }

    public function generateMs()
    {
        $html = '';
        foreach ($this->ms as $m) {
            $path = $this->faviconDir;
            $width = $m['width'];
            $height = $m['height'];
            $fileName = $m['content'].'-'.$width.'x'.$height.'.'.$m['ext'];
            $html .= '<meta name="'.$m['name'].'" content="'.$path.$fileName.$this->v.'" />';
        }
        return $html;
    }

    public function generateAndroid()
    {
        $html = '';
        foreach ($this->android as $a) {
            $path = $this->faviconDir;
            $width = $a['width'];
            $height = $a['height'];
            $fileName = $a['name'].'-'.$width.'x'.$height.'.'.$a['ext'];
            $html .= '<link rel="'.$a['rel'].'" type="'.$a['type'].'/'.$a['ext'].'" href="'.$path.$fileName.$this->v.'" sizes="'.$width.'x'.$height.'" />';
        }
        return $html;
    }

    public function generateLinks()
    {
        $html = '';
        $path = $this->faviconDir;
        $html .= '<link rel="manifest" href="'.$path.'manifest.json'.$this->v.'" />';
        return $html;
    }

}