<?php

namespace App\Libraries;

interface iTemplate
{
    public function display($view, $data);
    public function render($view, $data);
}