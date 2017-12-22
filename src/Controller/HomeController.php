<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use Twig_Environment;

class HomeController
{
    /** @var Twig_Environment */
    private $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function index()
    {
        $this->twig->display('home.html.twig');
    }
}