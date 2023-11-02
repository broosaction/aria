<?php

namespace Core\Joi\System\Stubs;

abstract class FaviconGenerator
{
    protected $faviconDir;
    protected $applicationName;
    protected $v;

    protected $apple = [
        ['name' => 'apple-icon', 'rel' => 'apple-touch-icon', 'width' => 57, 'height' => 57, 'ext' => 'png', 'background' => '#FFF'],
        ['name' => 'apple-icon', 'rel' => 'apple-touch-icon', 'width' => 60, 'height' => 60, 'ext' => 'png', 'background' => '#FFF'],
        ['name' => 'apple-icon', 'rel' => 'apple-touch-icon', 'width' => 72, 'height' => 72, 'ext' => 'png', 'background' => '#FFF'],
        ['name' => 'apple-icon', 'rel' => 'apple-touch-icon', 'width' => 114, 'height' => 114, 'ext' => 'png', 'background' => '#FFF'],
        ['name' => 'apple-icon', 'rel' => 'apple-touch-icon', 'width' => 76, 'height' => 76, 'ext' => 'png', 'background' => '#FFF'],
        ['name' => 'apple-icon', 'rel' => 'apple-touch-icon', 'width' => 120, 'height' => 120, 'ext' => 'png', 'background' => '#FFF'],
        ['name' => 'apple-icon', 'rel' => 'apple-touch-icon', 'width' => 152, 'height' => 152, 'ext' => 'png', 'background' => '#FFF'],
        ['name' => 'apple-icon', 'rel' => 'apple-touch-icon', 'width' => 180, 'height' => 180, 'ext' => 'png', 'background' => '#FFF']
    ];

    protected $appleStartupScreens = [
        ['name' => 'apple-startup', 'width' => 320, 'height' => 460, 'ext' => 'png', 'media' => '(device-width: 320px) and (device-height: 480px) and (-webkit-device-pixel-ratio: 1)', 'rel' => 'apple-touch-startup-image', 'background' => '#FFF', 'proportion' => 20],
        ['name' => 'apple-startup', 'width' => 640, 'height' => 920, 'ext' => 'png', 'media' => '(device-width: 320px) and (device-height: 480px) and (-webkit-device-pixel-ratio: 2)', 'rel' => 'apple-touch-startup-image', 'background' => '#FFF', 'proportion' => 20],
        ['name' => 'apple-startup', 'width' => 640, 'height' => 1096, 'ext' => 'png', 'media' => '(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)', 'rel' => 'apple-touch-startup-image', 'background' => '#FFF', 'proportion' => 20],
        ['name' => 'apple-startup', 'width' => 748, 'height' => 1024, 'ext' => 'png', 'media' => '(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 1) and (orientation: landscape)', 'rel' => 'apple-touch-startup-image', 'background' => '#FFF', 'proportion' => 20],
        ['name' => 'apple-startup', 'width' => 750, 'height' => 1024, 'ext' => 'png', 'media' => '', 'rel' => 'apple-touch-startup-image', 'background' => '#FFF', 'proportion' => 20],
        ['name' => 'apple-startup', 'width' => 750, 'height' => 1294, 'ext' => 'png', 'media' => '(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)', 'rel' => 'apple-touch-startup-image', 'background' => '#FFF', 'proportion' => 20],
        ['name' => 'apple-startup', 'width' => 768, 'height' => 1004, 'ext' => 'png', 'media' => '(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 1) and (orientation: portrait)', 'rel' => 'apple-touch-startup-image', 'background' => '#FFF', 'proportion' => 20],
        ['name' => 'apple-startup', 'width' => 1182, 'height' => 2208, 'ext' => 'png', 'media' => '(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)', 'rel' => 'apple-touch-startup-image', 'background' => '#FFF', 'proportion' => 20],
        ['name' => 'apple-startup', 'width' => 1242, 'height' => 2148, 'ext' => 'png', 'media' => '(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)', 'rel' => 'apple-touch-startup-image', 'background' => '#FFF', 'proportion' => 20],
        ['name' => 'apple-startup', 'width' => 1496, 'height' => 2048, 'ext' => 'png', 'media' => '(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)', 'rel' => 'apple-touch-startup-image', 'background' => '#FFF', 'proportion' => 20],
        ['name' => 'apple-startup', 'width' => 1536, 'height' => 2008, 'ext' => 'png', 'media' => '(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)', 'rel' => 'apple-touch-startup-image', 'background' => '#FFF', 'proportion' => 20]
    ];

    protected $ms = [
        ['name' => 'msapplication-TileImage', 'content' => 'ms-icon', 'width' => 144, 'height' => 144, 'ext' => 'png', 'background' => 'transparent'],
        ['name' => 'msapplication-square70x70logo', 'content' => 'ms-icon', 'width' => 70, 'height' => 70, 'ext' => 'png', 'background' => 'transparent'],
        ['name' => 'msapplication-square150x150logo', 'content' => 'ms-icon', 'width' => 150, 'height' => 150, 'ext' => 'png', 'background' => 'transparent'],
        ['name' => 'msapplication-wide310x150logo', 'content' => 'ms-icon', 'width' => 310, 'height' => 150, 'ext' => 'png', 'background' => 'transparent'],
        ['name' => 'msapplication-square310x310logo', 'content' => 'ms-icon', 'width' => 310, 'height' => 310, 'ext' => 'png', 'background' => 'transparent']
    ];

    protected $android = [
        ['name' => 'favicon', 'rel' => 'icon', 'type' => 'image', 'width' => 32, 'height' => 32, 'ext' => 'png', 'background' => 'transparent'],
        ['name' => 'android-icon', 'rel' => 'icon', 'type' => 'image', 'width' => 36, 'height' => 36, 'ext' => 'png', 'density' => '0.75', 'background' => 'transparent'],
        ['name' => 'android-icon', 'rel' => 'icon', 'type' => 'image', 'width' => 48, 'height' => 48, 'ext' => 'png', 'density' => '1.0', 'background' => 'transparent'],
        ['name' => 'android-icon', 'rel' => 'icon', 'type' => 'image', 'width' => 72, 'height' => 72, 'ext' => 'png', 'density' => '1.5', 'background' => 'transparent'],
        ['name' => 'android-icon', 'rel' => 'icon', 'type' => 'image', 'width' => 96, 'height' => 96, 'ext' => 'png', 'density' => '2.0', 'background' => 'transparent'],
        ['name' => 'android-icon', 'rel' => 'icon', 'type' => 'image', 'width' => 144, 'height' => 144, 'ext' => 'png', 'density' => '3.0', 'background' => 'transparent'],
        ['name' => 'android-icon', 'rel' => 'icon', 'type' => 'image', 'width' => 192, 'height' => 192, 'ext' => 'png', 'density' => '4.0', 'background' => 'transparent'],
        ['name' => 'favicon', 'rel' => 'icon', 'type' => 'image', 'width' => 96, 'height' => 96, 'ext' => 'png', 'background' => 'transparent'],
        ['name' => 'favicon', 'rel' => 'icon', 'type' => 'image', 'width' => 16, 'height' => 16, 'ext' => 'png', 'background' => 'transparent']
    ];

    public function __construct($applicationName, $faviconDir = 'assets/icons/')
    {
        $this->applicationName = $applicationName;
        $this->faviconDir = $faviconDir;
        $this->v = '?v=' . date('dmyhms');
    }

    public abstract function generate();

    protected function createFaviconDir($mode = 0777)
    {
        return is_dir($this->faviconDir) || mkdir($concurrentDirectory = $this->faviconDir, $mode, true) || is_dir($concurrentDirectory);
    }
}