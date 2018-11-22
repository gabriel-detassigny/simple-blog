<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\AboutPageController;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
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

    /** @var ExternalLinkService|Phake_IMock */
    private $externalLinkService;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    public function setUp()
    {
        parent::setUp();
        $this->blogInfoService = Phake::mock(BlogInfoService::class);
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->externalLinkService = Phake::mock(ExternalLinkService::class);
        $this->controller = new AboutPageController($this->twig, $this->blogInfoService, $this->externalLinkService);
    }

    public function testShowAboutPage(): void
    {
        Phake::when($this->blogInfoService)->getAboutText()->thenReturn(self::ABOUT_TEXT);
        Phake::when($this->externalLinkService)->getExternalLinks()->thenReturn([]);

        $this->controller->showAboutPage();

        Phake::verify($this->twig)->display('about.twig', ['aboutText' => self::ABOUT_TEXT, 'externalLinks' => []]);
    }
}
