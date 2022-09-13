<?php

namespace App\Libraries;

use DateTime;
use Exception;
use App\Libraries\Curl;

class Twitch
{
    protected $stringToken = 'w9l7ogivi2lpuvcr22dkxkf52u376e'; // '21hjoxipurkwsqisifut7f4edvinltux66n4kcx5sk6wni3i08';

    // [access_token] => w9l7ogivi2lpuvcr22dkxkf52u376e
    // [refresh_token] => juyiz40ekd60scvyp7o46i3rn1qd1mq3w4zzaiq8rdl0s94ucy

    protected $headers = [];
    protected $curl = null;
    protected $statusCode = 0;
    protected $clientId = null;
    protected $clientSecret = null;
    protected $userId = '';
    protected $redirectUri = '';
    protected $authorizedCode = '';
    protected $authUrl = 'https://id.twitch.tv/oauth2/token';
    protected $botList = [
        'nightbot', 'timeoutwithbits', 'streamlabs',
        'streamholics', 'streamelements', 'soundalerts',
        'bingcortana',
        'own3d', 'kaxips06',
        'blgdamjudge'
    ];

    public function __construct($params = [])
    {
        extract($params);
        if (isset($clientId)) $this->clientId = $clientId;
        if (isset($clientSecret)) $this->clientSecret = $clientSecret;
        if (isset($authorizedCode)) $this->authorizedCode = $authorizedCode;

        $this->redirectUri = ENVIRONMENT == 'production' ? '' : 'http://localhost:8000/gettwitch.php';
        $this->curl = new Curl();
    }
    public function getCredentials($params = [])
    {
        $storedTokenTwitch = '';
        extract($params);

        try {
            if (empty($storedTokenTwitch) || !$this->isValidToken(json_encode($storedTokenTwitch))) {
                throw new Exception('Invalid stored token');
            }
        } catch (Exception $e) {
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
            try {
                $content = json_decode($curlToken['content']);
                $this->stringToken = $content->access_token;
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
    }
    public function getRrefreshedToken($refreshToken)
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
    public function auth()
    {
        try {
            $data = array(
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $this->authorizedCode,
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
            pre($content);
            $this->stringToken = $content->access_token;
            $validate = $this->validateStringToken($this->stringToken);
            $refreshed = $this->getRrefreshedToken($content->refresh_token);
            pre($refreshed, 1);
            $now = new DateTime();
            $expira = clone $now;
            $expira->modify("+ $validate->expires_in seconds");
            $content->login = $validate->login;
            $content->exp = $expira;
            return $content;
        } catch (Exception $e) {
            return false;
        }
    }

    private function validateStringToken(String $token = null)
    {
        try {
            $return = $this->fetch('https://id.twitch.tv/oauth2/validate', 'get', [], ["Authorization: OAuth $token"]);
            pre($return, 1);
            if (property_exists($return, 'user_id'))
                $this->userId = $return->user_id;
            return $return;
        } catch (Exception $e) {
            return false;
        }
    }
    public function isValidToken($token)
    {
        $now = new DateTime();
        if ($token) {
            $expira = $token->exp;
            return $now < $expira;
        } else {
            return false;
        }

        return false;
    }

    public function getSubs()
    {
        $this->validateStringToken($this->stringToken);
        // $scope = urlencode('channel:read:subscriptions channel_subscriptions');
        // $uri_return = urlencode('http://localhost:8000/gettwitch.php');
        // $urlAuth = "https://id.twitch.tv/oauth2/authorize?response_type=code&client_id=$this->clientId&redirect_uri=$uri_return&scope=$scope";
        $return = $this->fetch('https://api.twitch.tv/helix/subscriptions', 'get', [
            'broadcaster_id' => $this->userId,
            'scope' => 'channel:read:subscriptions channel_subscriptions',
        ], ["Authorization: Bearer $this->stringToken", "Client-ID: $this->clientId"]);
        return $return;
    }
    public function fetch($url = '', $method = null, $data = [], $headers = [])
    {
        $this->statusCode = 0;
        try {
            $headers = $this->headers + $headers;
            if (str_starts_with($url, 'https://api.twitch.tv/helix/subscriptions')) {
                pre($headers);
            }
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
            return false;
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
}