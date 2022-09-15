<?php

namespace App\Config;

class CoreConfig
{
    public function get($item)
    {
        if (property_exists($this, $item))
            return $this->$item;
        else
            return false;
    }

    public function set($item, $valor)
    {
        if (property_exists($this, $item))
            $this->$item = $valor;
    }
}