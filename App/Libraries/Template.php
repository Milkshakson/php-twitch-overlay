<?php

namespace App\Libraries;

use ReflectionFunction;
use App\Libraries\ITemplate;

class Template implements ITemplate
{
    private $twig;

    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader(APPPATH . 'Views/Templates');
        $this->twig = new \Twig\Environment($loader, [
            //'cache' => 'app/Views/Templates/Cache',
            'autoescape' => false,
            'cache' => false,
        ]);
        // enable all php function on twig
        foreach (get_defined_functions() as $functions) {
            foreach ($functions as $functionName) {
                $details = new ReflectionFunction($functionName);
                $function = new \Twig\TwigFunction($details->name, $details->name);
                $this->twig->addFunction($function);
            }
        }
    }

    public function display($view, $data)
    {
        if (!str_ends_with($view, 'twig'))
            $view .= '.twig';
        echo $this->twig->render($view,  $data);
    }

    public function render($view, $data)
    {
        if (!str_ends_with($view, 'twig'))
            $view .= '.twig';
        return $this->twig->render($view, $data);
    }
}
