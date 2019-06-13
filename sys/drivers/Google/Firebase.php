<?php
/**
 * Created by PhpStorm.
 * User: broos
 * Date: 5/12/2019
 * Time: 19:26
 */

namespace Core\drivers\Google;


use Google_Client;

class Firebase
{


    /**
     * Firebase constructor.
     */
    public function __construct()
    {
        $client = new Google_Client();

        $client->useApplicationDefaultCredentials();

        //$client->setAuthConfig(__DIR__.'/auth.json');

        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $httpclient = $client->authorize();

        $message = [
            'messager' => '',
        ];

    }
}