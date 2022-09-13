<?php

namespace App\Libraries;

use DateTime;
use Exception;
use App\Libraries\Curl;

class Twitch
{
    protected $token = null;
    protected $headers = [];
    protected $curl = null;
    protected $statusCode = 0;
    protected $clientId = null;
    protected $clientSecret = null;
    protected $authUrl = 'https://id.twitch.tv/oauth2/token';

    public function __construct($params = [])
    {
        extract($params);
        if (isset($clientId)) $this->clientId = $clientId;
        if (isset($clientSecret)) $this->clientSecret = $clientSecret;
        $this->curl = new Curl();
    }
    public function auth(String $storedTokenTwitch = null)
    {
        try {
            $this->token = json_decode($storedTokenTwitch);
            if (!$this->isValidToken()) {
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
                $this->headers = ["Authorization: Bearer $content->access_token", "Client-ID: $this->clientId"];

                $now = new DateTime();
                $expira = clone $now;
                $expira->modify("+ $content->expires_in seconds");
                $content->exp = $expira;
                return $content;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function getToken($encode = false)
    {
        return $encode ? json_encode($this->token) : $this->token;
    }

    public function isValidToken()
    {
        $now = new DateTime();
        $token = $this->getToken();
        if ($token) {
            $expira = $token->exp;
            return $now < $expira;
        } else {
            return false;
        }

        return false;
    }

    public function fetch($url = '', $method = null, $data = [], $headers = [])
    {
        $this->statusCode = 0;
        try {
            $headers = $this->headers + $headers;
            $this->curl->setHeader($headers);
            $this->curl->setRequestType(strtoupper($method));
            $this->curl->setPost($data);
            $this->curl->createCurl($url);
            $this->statusCode = $this->curl->getHttpStatus();
            return json_decode($this->curl->__tostring());
        } catch (Exception $e) {
            return false;
        }
    }
}