<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\HomeController;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Twig_Environment;

class HomeControllerTest extends TestCase
{
    /** @var HomeController */
    private $controller;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->controller = new HomeController($this->twig);
    }

    public function testIndexWillDisplayTwigView()
    {
        $this->controller->index();

        Phake::verify($this->twig)->display('home.html.twig');
    }
}
