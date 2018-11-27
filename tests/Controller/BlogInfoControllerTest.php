<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\BlogInfoController;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class BlogInfoControllerTest extends TestCase
{
    private const BLOG_TITLE = 'Blog Title';
    private const BLOG_DESCRIPTION = 'blog description';
    private const ABOUT_TEXT = 'about text';

    /** @var BlogInfoService|Phake_IMock */
    private $blogInfoService;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /** @var AuthenticationService|Phake_IMock */
    private $authenticationService;

    /** @var BlogInfoController */
    private $controller;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->authenticationService = Phake::mock(AuthenticationService::class);
        $this->blogInfoService = Phake::mock(BlogInfoService::class);
        $this->controller = new BlogInfoController($this->twig, $this->blogInfoService, $this->authenticationService);
    }

    public function testEdit()
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->blogInfoService)->getBlogTitle()->thenReturn(self::BLOG_TITLE);
        Phake::when($this->blogInfoService)->getBlogDescription()->thenReturn(self::BLOG_DESCRIPTION);
        Phake::when($this->blogInfoService)->getAboutText()->thenReturn(self::ABOUT_TEXT);

        $this->controller->edit();

        Phake::verify($this->twig)->display(
            'blog-info/edit.twig',
            ['blogTitle' => self::BLOG_TITLE, 'blogDescription' => self::BLOG_DESCRIPTION, 'aboutText' => self::ABOUT_TEXT]
        );
    }

    public function testEdit_ForbiddenAccess()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->edit();
    }
}
