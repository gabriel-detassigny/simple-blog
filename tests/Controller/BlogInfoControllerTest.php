<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\BlogInfoController;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class BlogInfoControllerTest extends TestCase
{
    private const BLOG_TITLE = 'Blog Title';
    private const BLOG_DESCRIPTION = 'blog description';
    private const ABOUT_TEXT = 'about text';
    private const SUCCESS_MESSAGE = 'Blog Configuration successfully updated!';

    /** @var BlogInfoService|Phake_IMock */
    private $blogInfoService;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /** @var AuthenticationService|Phake_IMock */
    private $authenticationService;

    /** @var BlogInfoController */
    private $controller;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->authenticationService = Phake::mock(AuthenticationService::class);
        $this->blogInfoService = Phake::mock(BlogInfoService::class);
        $this->request = Phake::mock(ServerRequestInterface::class);
        $this->controller = new BlogInfoController(
            $this->twig,
            $this->blogInfoService,
            $this->authenticationService,
            $this->request
        );
    }

    public function testEdit(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->blogInfoService)->getBlogTitle()->thenReturn(self::BLOG_TITLE);
        Phake::when($this->blogInfoService)->getBlogDescription()->thenReturn(self::BLOG_DESCRIPTION);
        Phake::when($this->blogInfoService)->getAboutText()->thenReturn(self::ABOUT_TEXT);

        $this->controller->edit();

        Phake::verify($this->twig)->display(
            'blog-info/edit.twig',
            ['title' => self::BLOG_TITLE, 'description' => self::BLOG_DESCRIPTION, 'about' => self::ABOUT_TEXT]
        );
    }

    public function testEdit_ForbiddenAccess(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->edit();
    }

    public function testUpdate_ForbiddenAccess(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->update();
    }

    public function testUpdate_InvalidParams(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);

        $this->controller->update();
    }

    public function testUpdate(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()->thenReturn(
            [
                'blog' => [
                    'title' => self::BLOG_TITLE,
                    'description' => self::BLOG_DESCRIPTION,
                    'about' => self::ABOUT_TEXT
                ]
            ]
        );

        $this->controller->update();

        Phake::verify($this->twig)->display(
            'blog-info/edit.twig',
            [
                'title' => self::BLOG_TITLE,
                'description' => self::BLOG_DESCRIPTION,
                'about' => self::ABOUT_TEXT,
                'success' => self::SUCCESS_MESSAGE
            ]
        );
    }
}
