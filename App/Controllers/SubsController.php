<?php

namespace App\Controllers;

use App\Libraries\{Dotenv, Request, Session, Twitch};
use Exception;

class SubsController
{
    public function index()
    {
        $scope = 'channel:read:subscriptions';
        $env = new Dotenv();
        $session = new Session();
        $storedTokenTwitch = $session->get('tokenTwitch');
        $code = $session->get('codeTwitch');
        pre($code);
        $twitch = new Twitch([
            'clientId' => $env->get('clientIdTwitch'),
            'clientSecret' => $env->get('clientSecretTwitch'),
            'authorizedCode' => $code
        ]);
        // $twitch->getClientCredentials();
        // $twitch->getAuthorizationCode();
        // $twitch->auth($storedTokenTwitch);
        $subs = $twitch->getSubs();
        if (property_exists($subs, 'data')) {
            $lista = $subs->data;
            foreach ($lista as $sub) {
                echo "<h2>$sub->user_name</h2>";
            }
        } else
            pre($subs, 1);
    }
}