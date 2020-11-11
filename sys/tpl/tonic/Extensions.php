<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/13/2019
 * Time: 21:35
 */

namespace Core\tpl\tonic;


use App\Models\Projects;
use Core\joi\Start;
use Nette\Caching\Cache;


class Extensions
{

    private $tonic;
    private $server;

    /**
     * Extensions constructor.
     * @param Tonic $tonic
     * @param Start $server
     */
    public function __construct(Tonic $tonic, Start $server)
    {
        $this->server = $server;
        if (!isset($tonic)) {

            $tonic = new Tonic(); // dev level view leveler.
        }
        $this->tonic = $tonic;


        new ExtMath($this->tonic);
        new ExtGithub($this->tonic);
        new ExtCache($this->tonic, $this->server);
        new ExtDateTime($this->tonic);
     

        $this->extendTonic();
    }


    private function extendTonic()
    {
        $ses = $this->server->getSessions();
        $config = $this->server->getConfig();
        $server = $this->server;
        $tpl = $this->tonic;
        $this->tonic::extendModifier('test', static function ($input) {
            return 'Aria works';
        });

        $this->tonic::extendModifier('sesid', static function ($input) use ($ses) {
            return $ses::getId();
        });

        $this->tonic::extendModifier('echo', static function ($input) use ($ses) {
            echo $input;
        });
        $this->tonic::extendModifier('stripplus', static function ($input) use ($ses) {
            echo str_replace('+', ' ', $input);
        });
        $this->tonic::extendModifier('tochips', static function ($input) use ($ses) {
            $chips = explode(',', $input);
            for ($i = 0, $iMax = count($chips); $i <= $iMax - 1; $i++) {
                echo '<div class="chip">' . $chips[$i] . '</div>';
            }
        });


        $this->tonic::extendModifier('bing_get_image', static function ($input) use ($ses, $config) {
            $bing_daily_image_xml = file_get_contents('https://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1&mkt=en-US');
            if ($bing_daily_image_xml !== '') {
                $matches = json_decode($bing_daily_image_xml);
                $bing_daily_img_url = 'https://s.cn.bing.com' . $matches->images[0]->url;
                echo $bing_daily_img_url;
            } else {
                echo 'http://' . $config->getThemeUrl() . '/assets/img/bg1.png';
            }
        });


        $this->tonic::extendModifier('selfurl', static function ($input) use ($ses) {

            $s = (empty($_SERVER['HTTPS']) ? ''
                : ($_SERVER['HTTPS'] === 'on')) ? "s"
                : '';
            $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], '/') . $s));
            $port = ($_SERVER['SERVER_PORT'] === '80') ? ''
                : (':' . $_SERVER['SERVER_PORT']);
            return $protocol . '://' . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];

        });

        $this->tonic::extendModifier('getappsub', static function ($input) use ($server, $tpl) {

            $proj = new Projects($server, $tpl);

            if($server->getCache()->load('appsub'.$input)){
                return $server->getCache()->load('appsub'.$input);
            }
            $count = count($proj->getAppSubs((int)$input));
            $server->getCache()->save('appsub'.$input, $count, array(
                Cache::EXPIRE => '2 minutes', // accepts also seconds or a timestamp.
                Cache::SLIDING => true,
            ));
            return $count;
        });

        $this->tonic::extendModifier('getextinstalls', static function ($input) use ($server, $tpl) {

            $proj = new \App\Models\Extensions($server, $tpl);
            if($server->getCache()->load('installs'.$input)){
                return $server->getCache()->load('installs'.$input);
            }
            $count = count($proj->getextensionsubs((int)$input));
            $server->getCache()->save('installs'.$input, $count, array(
                Cache::EXPIRE => '2 minutes', // accepts also seconds or a timestamp.
                Cache::SLIDING => true,
            ));
            return $count;
        });


    }
}