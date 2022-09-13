<?php

namespace App\Libraries;

use Exception;

class Dotenv
{
    private $env = [];
    public function __construct($onlyDotEnv = false)
    {
        try {
            $fileEnv = ROOTDIR . '.env';
            if (file_exists($fileEnv)) {
                $parsed = parse_ini_file($fileEnv);
                $this->env = $onlyDotEnv ? $parsed : array_merge($_ENV, $parsed);
            } else {
                $this->env = $onlyDotEnv ? [] : $_ENV;
            }
        } catch (Exception $e) {
            $this->env = [];
        }
    }
    public function get($item)
    {
        return key_exists($item, $this->env) ? $this->env[$item] : null;
    }
}