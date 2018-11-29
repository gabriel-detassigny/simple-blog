<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Controller\AdminIndexController;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use GabrielDeTassigny\Blog\ValueObject\Page;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class AdminIndexControllerTest extends TestCase
{
    private const BLOG_TITLE = 'Blog Title';

    /** @var AdminIndexController */
    private $controller;

    /** @var PostViewingService|Phake_IMock */
    private $postViewingService;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /** @var AuthenticationService|Phake_IMock */
    private $authenticationService;

    /** @var AuthorService|Phake_IMock */
    private $authorService;

    /** @var BlogInfoService|Phake_IMock */
    private $blogInfoService;

    /** @var ExternalLinkService|Phake_IMock */
    private $externalLinkService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->authenticationService = Phake::mock(AuthenticationService::class);
        $this->authorService = Phake::mock(AuthorService::class);
        $this->postViewingService = Phake::mock(PostViewingService::class);
        $this->blogInfoService = Phake::mock(BlogInfoService::class);
        $this->externalLinkService = Phake::mock(ExternalLinkService::class);

        $this->controller = new AdminIndexController(
            $this->twig,
            $this->authenticationService,
            $this->postViewingService,
            $this->authorService,
            $this->blogInfoService,
            $this->externalLinkService
        );
    }

    public function testIndex()
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        $posts = Phake::mock(Paginator::class);
        Phake::when($this->postViewingService)->findPageOfLatestPosts(new Page(1))
            ->thenReturn($posts);
        Phake::when($this->authorService)->getAuthors()->thenReturn([]);
        Phake::when($this->blogInfoService)->getBlogTitle()->thenReturn(self::BLOG_TITLE);
        Phake::when($this->externalLinkService)->getExternalLinks()->thenReturn([]);

        $this->controller->index();

        Phake::verify($this->twig)->display(
            'admin-index.twig',
            ['posts' => $posts, 'authors' => [], 'title' => self::BLOG_TITLE, 'externalLinks' => []]
        );
    }

    public function testIndex_ForbiddenAccess()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->index();
    }
}
