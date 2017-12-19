<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Twig_Environment;

class HomeController
{
    /** @var ServerRequestInterface */
    private $serverRequest;

    /** @var Twig_Environment */
    private $twig;

    public function __construct(ServerRequestInterface $serverRequest, Twig_Environment $twig)
    {
        $this->serverRequest = $serverRequest;
        $this->twig = $twig;
    }

    public function index(array $args)
    {
        echo $this->twig->render('home.html.twig');
    }
}