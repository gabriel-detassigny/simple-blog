<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\AboutPageController;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Twig_Environment;

class AboutPageControllerTest extends TestCase
{
    private const ABOUT_TEXT = 'this blog is about...';

    /** @var AboutPageController */
    private $controller;

    /** @var BlogInfoService|Phake_IMock */
    private $blogInfoService;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    public function setUp()
    {
        parent::setUp();
        $this->blogInfoService = Phake::mock(BlogInfoService::class);
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->controller = new AboutPageController($this->twig, $this->blogInfoService);
    }

    public function testShowAboutPage()
    {
        Phake::when($this->blogInfoService)->getAboutText()->thenReturn(self::ABOUT_TEXT);

        $this->controller->showAboutPage();

        Phake::verify($this->twig)->display('about.twig', ['aboutText' => self::ABOUT_TEXT]);
    }
}
