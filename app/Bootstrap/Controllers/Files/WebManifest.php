<?php
/**
 * Copyright (c) 2021.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by Bruce Mubangwa on 02 /Jun, 2021 @ 21:12
 */

namespace App\Bootstrap\Controllers\Files;


use Core\Router\Controllers\BaseController;

class WebManifest extends BaseController
{

    /**
     * @method ['get', 'post']
     * @path /site.webmanifest
     * @id siteWebmanifest
     * defaultParameterRegex .*?
     */
    public function siteWebmanifest()
    {
        $server = $this->server;
        header("Content-Type: application/json; charset=utf-8");
        header("Server: 	Broos Action/2.4.34 (Win64) OpenSSL/1.1.0h {$server->getConfig()->app_name}/7.2.4");
        error_reporting(E_ALL);
        echo '{
    "name": "' . $server->getConfig()->app_name . ' Cloud",
    "short_name": "' . $server->getConfig()->app_name . '",
    "icons": [
        {
            "src": "' . $server->getConfig()->app_protocol . '://' . $server->getConfig()->app_url . '/themes/default/assets/favicon/android-chrome-36x36.png",
            "sizes": "36x36",
            "type": "image/png"
        },
        {
            "src": "' . $server->getConfig()->app_protocol . '://' . $server->getConfig()->app_url . '/themes/default/assets/favicon/android-chrome-48x48.png",
            "sizes": "48x48",
            "type": "image/png"
        },
        {
            "src": "' . $server->getConfig()->app_protocol . '://' . $server->getConfig()->app_url . '/themes/default/assets/favicon/android-chrome-72x72.png",
            "sizes": "72x72",
            "type": "image/png"
        },
        {
            "src": "' . $server->getConfig()->app_protocol . '://' . $server->getConfig()->app_url . '/themes/default/assets/favicon/android-chrome-96x96.png",
            "sizes": "96x96",
            "type": "image/png"
        },
        {
            "src": "' . $server->getConfig()->app_protocol . '://' . $server->getConfig()->app_url . '/themes/default/assets/favicon/android-chrome-144x144.png",
            "sizes": "144x144",
            "type": "image/png"
        },
        {
            "src": "' . $server->getConfig()->app_protocol . '://' . $server->getConfig()->app_url . '/themes/default/assets/favicon/android-chrome-192x192.png",
            "sizes": "192x192",
            "type": "image/png"
        },
        {
            "src": "' . $server->getConfig()->app_protocol . '://' . $server->getConfig()->app_url . '/themes/default/assets/favicon/android-chrome-256x256.png",
            "sizes": "256x256",
            "type": "image/png"
        },
        {
            "src": "' . $server->getConfig()->app_protocol . '://' . $server->getConfig()->app_url . '/themes/default/assets/favicon/android-chrome-384x384.png",
            "sizes": "384x384",
            "type": "image/png"
        },
        {
            "src": "' . $server->getConfig()->app_protocol . '://' . $server->getConfig()->app_url . '/themes/default/assets/favicon/android-chrome-512x512.png",
            "sizes": "512x512",
            "type": "image/png"
        }
    ],
    "theme_color": "#ffffff",
    "background_color": "#ffffff",
    "start_url": "' . $server->getConfig()->app_protocol . '://' . $server->getConfig()->app_url . '",
    "display": "standalone"
}
';


    }

}