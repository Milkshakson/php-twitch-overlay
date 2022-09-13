<?php

namespace App\Libraries;

class Session
{
    public function __construct()
    {
        if (session_status() != PHP_SESSION_ACTIVE)
            session_start();
    }
    public function get($key)
    {
        return key_exists($key, $_SESSION) ? $_SESSION[$key] : false;
    }
    public function set($key, $data)
    {
        $_SESSION[$key] = $data;
    }
}
