<?php

namespace App\Controllers;

use App\Libraries\Dotenv;
use App\Libraries\Session;
use App\Libraries\Twitch;
use App\Models\StreamerModel;
use Exception;

class TwitchController  extends BaseController
{


    public function authorize()
    {
        $scope = urlencode('channel:read:subscriptions channel_subscriptions');
        $twitch = new Twitch();
        $uri_return = urlencode($twitch->getRedirectUri());
        $env = new Dotenv();
        $clientId = $env->get('clientIdTwitch');
        $this->dados['urlAuth'] = "https://id.twitch.tv/oauth2/authorize?response_type=code&client_id=$clientId&redirect_uri=$uri_return&scope=$scope";
        $this->display('Twitch/authorize');
    }

    public function authorizeComplete()
    {
        try {
            $env = new Dotenv();
            if (key_exists('code', $_GET)) {
                $twitch = new Twitch([
                    'clientId' => $env->get('clientIdTwitch'),
                    'clientSecret' => $env->get('clientSecretTwitch'),
                ]);
                $code = $_GET['code'];
                $credentials = $twitch->getAuthorizationCode($code);
                if ($credentials && property_exists($credentials, 'userId') && $credentials->userId > 0) {
                    $streamerModel = new StreamerModel();
                    if ($streamerModel->saveAuthorization($credentials)) {
                        return '<h2>Credenciais salvas com sucesso.</h2>';
                    } else {
                        return '<h2>Falha ao salvar as credenciais.</h2>';
                    }
                } else {
                    return '<h2>Falha ao salvar as credenciais.</h2>';
                }
            } else {
                return '<h2>Código não autorizado.</h2>';
            }
        } catch (Exception $e) {
            return '<h2>Falha ao salvar as credenciais.</h2>' . '<pre>' . json_encode($e) . '</pre>';
        }
    }
}