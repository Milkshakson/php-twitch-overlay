<?php

namespace App\Config;

use App\Libraries\Dotenv;

class Url extends CoreConfig
{
    protected $baseUrl;
    public function __construct()
    {
        $dotenv = new Dotenv();
        $baseUrl = $dotenv->get('baseUrl');
        $this->baseUrl = ($baseUrl ? $baseUrl : $_SERVER['HTTP_HOST']) . '/';
    }
}