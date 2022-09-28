<?php

namespace App\Controllers;

use App\Libraries\Template;

class BaseController
{
    protected $template;
    protected $dados = [];
    public function __construct()
    {
        $this->template = new Template();
    }
    protected function display($view, $dados = null)
    {
        if (!is_null($dados))
            $this->dados = array_merge($this->dados, $dados);
        $this->template->display($view, $this->dados);
        exit;
    }

    protected function render($view, $dados = null)
    {
        if (is_null($dados))
            $this->dados = array_merge($this->dados, $dados);
        return $this->template->render($view, $this->dados);
    }
}