<?php

namespace Core\Drivers\Icons;

use Core\Joi\System\Stubs\FaviconGenerator;

class IconGenerator extends FaviconGenerator
{
    private $faviconFilePath;
    private $appleFilePath;
    private $appleStartupImageProportion;

    public function __construct($applicationName, $faviconDir, $faviconFilePath, $appleFilePath, $appleStartupImageProportion = 20.0)
    {
        parent::__construct($applicationName, $faviconDir);
        $this->faviconFilePath = $faviconFilePath;
        $this->appleFilePath = $appleFilePath;
        $this->appleStartupImageProportion = $appleStartupImageProportion;
    }

    public function generate()
    {
        if ($this->createFaviconDir()) {
            $this->generateAppleImages();
            $this->generateAppleStartupImages($this->appleStartupImageProportion);
            $this->generateMsImages();
            $this->generateAndroidImages();
            $this->generateManifestJson();
        }
    }

    private function hex2RGB($hex)
    {
        $hex = str_replace("#", "", strtolower($hex));
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return array($r, $g, $b);
    }

    private function desiredSize($o_w, $o_h, $n_w, $n_h)
    {
        $n_r = $n_w / $n_h;
        $o_r = $o_w / $o_h;
        if ($n_r >= 1) {
            if ($o_r >= 1) {
                if ($n_r >= $o_r) {
                    $w = $o_w / ($o_h / $n_h);
                    $h = $n_h;
                } else {
                    $w = $n_w;
                    $h = $o_h / ($o_w / $n_w);
                }
            } else {
                $w = $o_w / ($o_h / $n_h);
                $h = $n_h;
            }
        } else {
            if ($o_r >= 1) {
                $w = $n_w;
                $h = $o_h / ($o_w / $n_w);
            } else {
                if ($n_r >= $o_r) {
                    $w = $o_w / ($o_h / $n_h);
                    $h = $n_h;
                } else {
                    $w = $n_w;
                    $h = $o_h / ($o_w / $n_w);
                }
            }
        }
        return ['width' => $w, 'height' => $h];
    }

    public function resizeImage($source, $target, $newWidth, $newheight, $background = 'transparent', $proportion = 100.0)
    {
        if (!file_exists($source)) {
            return false;
        }

        if (preg_match('/#([a-f\d]{3}){1,2}\b/i', $background)) {
            $background = $this->hex2RGB($background);
        }

        if ($proportion >= 10.0 && $proportion <= 100.0) {
            $proportion /= 100.0;
        } else {
            $proportion = 1.0;
        }

        $info = getimagesize($source);
        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg':
                $imageCreateFunction = 'imagecreatefromjpeg';
                $imageSaveFunction = 'imagejpeg';
                $newImageExt = 'jpg';
                break;

            case 'image/png':
                $imageCreateFunction = 'imagecreatefrompng';
                $imageSaveFunction = 'imagepng';
                $newImageExt = 'png';
                break;

            case 'image/gif':
                $imageCreateFunction = 'imagecreatefromgif';
                $imageSaveFunction = 'imagegif';
                $newImageExt = 'gif';
                break;

            default:
                throw new Exception('Unknown image type.');
        }

        list($widthOrig, $heightOrig) = getimagesize($source);

        $size = $this->desiredSize($widthOrig, $heightOrig, $newWidth, $newheight);

        $new_width = ceil($size['width'] * $proportion);
        $new_height = ceil($size['height'] * $proportion);

        $x_mid = $new_width / 2;
        $y_mid = $new_height / 2;

        $myImage = $imageCreateFunction($source);
        $newImage = imagecreatetruecolor($newWidth, $newheight);

        if ($background === 'transparent') {
            imagesavealpha($newImage, true);
            $black = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
            imagefill($newImage, 0, 0, $black);
        } elseif (is_array($background) && count($background) === 3) {
            $rgb = $background;
            $color = imagecolorallocate($newImage, $rgb[0], $rgb[1], $rgb[2]);
            imagefill($newImage, 0, 0, $color);
            imagealphablending($myImage, false);
            imagesavealpha($myImage, true);
        }

        imagecopyresampled($newImage, $myImage, (($newWidth / 2)- ($x_mid)), (($newheight / 2) - $y_mid), 0, 0, $new_width, $new_height, $widthOrig, $heightOrig);

        imagedestroy($myImage);

        $imageSaveFunction($newImage, "$target.$newImageExt");

        return $newImage;
    }

    public function generateAppleImages()
    {
        foreach ($this->apple as $ap) {
            $width = $ap['width'];
            $height = $ap['height'];
            $extensao = $ap['ext'];
            $fileName = $ap['name'].'-'.$width.'x'.$height;
            $background = $ap['background'];
            $this->resizeImage($this->faviconFilePath, $this->faviconDir.$fileName, $width, $height, $background);
        }
        $this->resizeImage($this->faviconFilePath, $this->faviconDir.'apple-icon', 192, 192, $background);
        $this->resizeImage($this->faviconFilePath, $this->faviconDir.'apple-icon-precomposed', 192, 192, $background);
    }

    public function generateAppleStartupImages($proportion)
    {
        foreach ($this->appleStartupScreens as $ap) {
            $width = $ap['width'];
            $height = $ap['height'];
            $extensao = $ap['ext'];
            $fileName = $ap['name'].'-'.$width.'x'.$height;
            $background = $ap['background'];
            $proportion = $proportion ?? $ap['proportion'];
            $this->resizeImage($this->appleFilePath, $this->faviconDir.$fileName, $width, $height, $background, $proportion);
        }
    }

    public function generateMsImages()
    {
        foreach ($this->ms as $m) {
            $width = $m['width'];
            $height = $m['height'];
            $extensao = $m['ext'];
            $fileName = $m['content'].'-'.$width.'x'.$height;
            $background = $m['background'];
            $this->resizeImage($this->faviconFilePath, $this->faviconDir.$fileName, $width, $height, $background);
        }
    }

    public function generateAndroidImages()
    {
        foreach ($this->android as $a) {
            $width = $a['width'];
            $height = $a['height'];
            $extensao = $a['ext'];
            $fileName = $a['name'].'-'.$width.'x'.$height;
            $background = $a['background'];
            $this->resizeImage($this->faviconFilePath, $this->faviconDir.$fileName, $width, $height, $background);
        }
    }


    private function generateManifestJson()
    {
        $path = $this->faviconDir;
        $manifest = [];
        $manifest['name'] = $this->applicationName;
        $icons = [];
        foreach ($this->android as $a) {
            if (isset($a['density'])) {
                $icon = [];
                $width = $a['width'];
                $height = $a['height'];
                $sizes = $width.'x'.$height;
                $ext = $a['ext'];
                $density = $a['density'];
                $fileName = $a['name'].'-'.$sizes.'.'.$ext;
                $type = $a['type'].'/'.$ext;
                $icon['src'] = '/'.$path.$fileName;
                $icon['sizes'] = $sizes;
                $icon['type'] = $type;
                $icon['density'] = $density;
                $icons[] = $icon;
            }
        }
        $manifest['icons'] = $icons;
        $fp = fopen($path.'manifest.json', 'w');
        fwrite($fp, json_encode($manifest));
        fclose($fp);
    }

    public function setAppleStartupImageProportion($appleStartupImageProportion)
    {
        $this->appleStartupImageProportion = $appleStartupImageProportion;
    }

    public function getAppleStartupImageProportion ()
    {
        return $this->appleStartupImageProportion;
    }
}