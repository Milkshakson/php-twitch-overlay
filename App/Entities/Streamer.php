<?php

namespace App\Entities;

class Streamer
{
    private $authorizationCode = null;
    private $broadcasterId = null;
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
}