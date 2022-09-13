<?php

namespace App\Libraries;

class Request
{
    public function find($key = '')
    {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'POST':
                return $this->getFromArray($key, $_POST);
                break;
            case 'GET':
                return $this->getFromArray($key, $_GET);
                break;
            default:
                return null;
                break;
        }
    }

    private function getFromArray($key = null, $array = [])
    {
        return key_exists($key, $array) ? $array[$key] : null;
    }
}
