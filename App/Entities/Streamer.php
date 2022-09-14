<?php

namespace App\Entities;

use stdClass;

class Streamer
{
    private $authorizationCode = null;
    private $broadcasterId = null;
    private $accessToken = null;
    private $credentials = null;
    public function __construct()
    {
    }

    /**
     * Get the value of authorizationCode
     */
    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }

    /**
     * Set the value of authorizationCode
     *
     * @return  self
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;

        return $this;
    }

    /**
     * Get the value of broadcasterId
     */
    public function getBroadcasterId()
    {
        return $this->broadcasterId;
    }

    /**
     * Set the value of broadcasterId
     *
     * @return  self
     */
    public function setBroadcasterId($broadcasterId)
    {
        $this->broadcasterId = $broadcasterId;

        return $this;
    }

    /**
     * Get the value of accessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set the value of accessToken
     *
     * @return  self
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get the value of credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Set the value of credentials
     *
     * @return  self
     */
    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;

        return $this;
    }
}