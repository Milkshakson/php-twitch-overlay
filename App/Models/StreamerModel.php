<?php

namespace App\Models;

use App\Entities\Streamer;
use App\Libraries\Dotenv;
use App\Libraries\Session;
use App\Libraries\Twitch;

class StreamerModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function findByName($name)
    {
        $session = new Session();

        //retorna a lista de subs do Streamer
        $env = new Dotenv();
        $twitch = new Twitch([
            'clientId' => $env->get('clientIdTwitch'),
            'clientSecret' => $env->get('clientSecretTwitch'),
        ]);
        //pre($session->get('validAuth'));
        $info = $twitch->getUserInfo($name);
        $streamer = new Streamer();
        $credentials = $session->get('validAuth');
        $streamer->setCredentials($credentials);
        return $streamer;
    }

    public function getStreamercredentials($streamer)
    {
        // if (!$auth) {
        //     $auth = $this->getAuthorizationCode($streamer->getAuthorizationCode());
        // }

        // return $auth;
    }

    public function getSubList(Streamer $streamer)
    {
        //retorna a lista de subs do Streamer
        $env = new Dotenv();
        $twitch = new Twitch([
            'clientId' => $env->get('clientIdTwitch'),
            'clientSecret' => $env->get('clientSecretTwitch'),
        ]);
        $subList = $twitch->getSubList($streamer->getCredentials());
        if ($subList && property_exists($subList, 'refreshedCredential')) {
            $session = new Session();
            $session->set('validAuth', $subList->refreshedCredential);
        }
        $newArray = [];
        if (property_exists($subList, 'data')) {
            foreach ($subList->data as $sub) {
                $newArray[$sub->user_name] = $sub;
            }
        }
        return $newArray;
    }
}