<?php

namespace App\Config;

class Helpers
{
    private $defaults = ['common'];
    public function __construct()
    {
    }

    public function loadDefaults()
    {
        foreach ($this->defaults as $helper) {
            $pathHelper = ROOTDIR . 'App/Helpers/' . ucfirst($helper) . 'Helper.php';
            if (file_exists($pathHelper))
                include_once($pathHelper);
        }
    }

    public function load($helpers = [])
    {
        if (is_array($helpers)) {
            foreach ($helpers as $helper) {
                $pathHelper = ROOTDIR . 'App/Helpers/' . ucfirst($helper) . 'Helper.php';
                if (file_exists($pathHelper))
                    include_once($pathHelper);
            }
        } else if (is_string($helpers)) {
            $pathHelper = ROOTDIR . 'App/Helpers/' . ucfirst($helpers) . 'Helper.php';
            if (file_exists($pathHelper))
                include_once($pathHelper);
        }
    }
}