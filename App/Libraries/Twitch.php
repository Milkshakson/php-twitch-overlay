<?php

namespace App\Libraries;

use DateTime;
use Exception;
use App\Libraries\Curl;

class Twitch
{
    protected $headers = [];
    protected $curl = null;
    protected $statusCode = 0;
    protected $clientId = null;
    protected $clientSecret = null;
    protected $redirectUri = '';
    protected $authUrl = 'https://id.twitch.tv/oauth2/token';
    protected $botList = [
        'nightbot', 'timeoutwithbits', 'streamlabs',
        'streamholics', 'streamelements', 'soundalerts',
        'bingcortana',
        'own3d', 'kaxips06',
        'blgdamjudge'
    ];

    protected $translatedmessages = [
        'invalid access token' => 'Token de acesso inválido',
        'Invalid authorization code' => 'Código de autorização inválido.'
    ];
    public function __construct($params = [])
    {
        extract($params);
        if (isset($clientId)) $this->clientId = $clientId;
        if (isset($clientSecret)) $this->clientSecret = $clientSecret;

        $this->redirectUri = ENVIRONMENT == 'production' ? '' : 'http://localhost:8000/twitch/authorize-complete';
        $this->curl = new Curl();
    }
    public function getClientCredentials()
    {
        try {
            $data = array(
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials',
            );
            $this->curl->setRequestType('POST');
            $this->curl->setPost($data);
            $this->curl->createCurl($this->authUrl);
            $curlToken = [
                "status_code" => $this->curl->getHttpStatus(),
                "content" => $this->curl->__tostring()
            ];
            $content = json_decode($curlToken['content']);
            $validate = $this->validateStringToken($content->access_token);
            $now = new DateTime();
            $expira = clone $now;
            $expira->modify("+ $validate->expires_in seconds");
            $content->exp = $expira;
            return $content;
        } catch (Exception $e) {
            return false;
        }
    }
    public function getAuthorizationCode($authorizedCode)
    {
        try {
            $data = array(
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $authorizedCode,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->redirectUri
            );
            $this->curl->setRequestType('POST');
            $this->curl->setPost($data);
            $this->curl->createCurl($this->authUrl);
            $curlToken = [
                "status_code" => $this->curl->getHttpStatus(),
                "content" => $this->curl->__tostring()
            ];
            $content = json_decode($curlToken['content']);
            $validate = $this->validateStringToken($content->access_token);
            $refreshed = $this->getRrefreshedToken($content->refresh_token);
            $this->refreshToken = $refreshed->refresh_token;
            $now = new DateTime();
            $expira = clone $now;
            $expira->modify("+ $validate->expires_in seconds");
            $content->login = $validate->login;
            $content->exp = $expira;
            $content->userId = $validate->user_id;
            return $content;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getRrefreshedToken($refreshToken)
    {
        try {
            $data = array(
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token',
                'refresh_token' => urlencode($refreshToken)
            );
            $this->curl->setRequestType('POST');
            $this->curl->setPost($data);
            $this->curl->createCurl($this->authUrl);
            $curlToken = [
                "status_code" => $this->curl->getHttpStatus(),
                "content" => $this->curl->__tostring()
            ];
            $content = json_decode($curlToken['content']);
            return $content;
        } catch (Exception $e) {
            return false;
        }
    }
    private function validateStringToken(String $token = null)
    {
        try {
            $return = $this->fetch('https://id.twitch.tv/oauth2/validate', 'get', [], ["Authorization: OAuth $token"]);
            return $return;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getSubList($credentials)
    {
        /**
         * $credentials must be a object returned by TWITCH API with attributes:
         * user_id
         * refresh_token
         * access_token
         * expires_in
         */
        $broadcasterId = $credentials->userId;
        $return = $this->fetch('https://api.twitch.tv/helix/subscriptions', 'get', [
            'broadcaster_id' => $broadcasterId,
            'scope' => 'channel:read:subscriptions channel_subscriptions',
        ], ["Authorization: Bearer $credentials->access_token", "Client-ID: $this->clientId"]);
        $refreshed = $this->getRrefreshedToken($credentials->refresh_token);
        $credentials->access_token = $refreshed->access_token;
        $credentials->refresh_token = $refreshed->refresh_token;
        $return->refreshedCredential = $credentials;
        return $return;
    }
    public function fetch($url = '', $method = null, $data = [], $headers = [])
    {
        $this->statusCode = 0;
        try {
            $headers = $this->headers + $headers;
            $this->curl->setHeader($headers);
            $this->curl->setRequestType(strtoupper($method));
            if (strtolower($method) == 'get' && count($data)) {
                $url .= '?';
                foreach ($data as $key => $value) {
                    $url .= "&$key=" . urlencode($value);
                }
            } else {
                $this->curl->setPost($data);
            }
            $this->curl->createCurl($url);
            $this->statusCode = $this->curl->getHttpStatus();
            return json_decode($this->curl->__tostring());
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getChatters($streamer = '', $noBots = true)
    {
        $url = "https://tmi.twitch.tv/group/user/$streamer/chatters";
        $users = file_get_contents($url);
        $users = json_decode($users);
        $chatters = $users->chatters;
        $viewers = [];
        foreach ($chatters->viewers as $chatter) {
            if (!$noBots || !$this->isBot($chatter))
                $viewers[] = ['nome' => $chatter, 'tipo' => 'viewers'];
        }
        foreach ($chatters->moderators as $chatter) {
            if (!$noBots || !$this->isBot($chatter))
                $viewers[] = ['nome' => $chatter, 'tipo' => 'moderators'];
        }
        return $viewers;
    }

    private function isBot($viewer = '')
    {
        return in_array($viewer, $this->botList);
    }

    private function translateMessage($message = null)
    {
        return key_exists($message, $this->translatedmessages) ? $this->translatedmessages[$message] : $message;
    }

    /**
     * Get the value of clientId
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set the value of clientId
     *
     * @return  self
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get the value of redirectUri
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }
}