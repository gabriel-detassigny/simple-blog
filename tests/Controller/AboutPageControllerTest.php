<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\AboutPageController;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class AboutPageControllerTest extends TestCase
{
    private const ABOUT_TEXT = 'this blog is about...';
    private const BLOG_TITLE = 'Blog Title';

    /** @var AboutPageController */
    private $controller;

    /** @var BlogInfoService|Phake_IMock */
    private $blogInfoService;

    /** @var AuthorService|Phake_IMock */
    private $authorService;

    /** @var Environment|Phake_IMock */
    private $twig;

    public function setUp(): void
    {
        $this->blogInfoService = Phake::mock(BlogInfoService::class);
        $this->twig = Phake::mock(Environment::class);
        $this->authorService = Phake::mock(AuthorService::class);

        $this->controller = new AboutPageController($this->twig, $this->blogInfoService, $this->authorService);
    }

    public function testShowAboutPage(): void
    {
        $authors = [Phake::mock(Author::class), Phake::mock(Author::class)];
        Phake::when($this->authorService)->getAuthors()->thenReturn($authors);

        Phake::when($this->blogInfoService)->getAboutText()->thenReturn(self::ABOUT_TEXT);
        Phake::when($this->blogInfoService)->getBlogTitle()->thenReturn(self::BLOG_TITLE);

        $this->controller->showAboutPage();

        Phake::verify($this->twig)->display('about.twig', [
            'aboutText' => self::ABOUT_TEXT,
            'authors' => $authors,
            'blogTitle' => self::BLOG_TITLE
        ]);
    }
}
