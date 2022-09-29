<?php

namespace App\Libraries;

interface ITemplate
{
    public function display($view, $data);
    public function render($view, $data);
}