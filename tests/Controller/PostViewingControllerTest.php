<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Controller\PostViewingController;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\CommentService;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use GabrielDeTassigny\Blog\Service\PostNotFoundException;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class PostViewingControllerTest extends TestCase
{
    const POST_ID = 1;

    /** @var PostViewingController */
    private $controller;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /** @var PostViewingService|Phake_IMock */
    private $postService;

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
        $this->postService = Phake::mock(PostViewingService::class);
        $this->blogInfoService = Phake::mock(BlogInfoService::class);
        $this->externalLinkService = Phake::mock(ExternalLinkService::class);
        $this->controller = new PostViewingController(
            $this->twig,
            $this->postService,
            $this->blogInfoService,
            $this->externalLinkService
        );

        Phake::when($this->postService)->getPreviousPage(Phake::anyParameters())->thenReturn(null);
        Phake::when($this->postService)->getNextPage(Phake::anyParameters())->thenReturn(null);
        Phake::when($this->externalLinkService)->getExternalLinks()->thenReturn([]);
    }

    public function testIndexWillDisplayTwigView(): void
    {
        $posts = Phake::mock(Paginator::class);
        Phake::when($this->postService)->findPageOfLatestPosts(Phake::anyParameters())->thenReturn($posts);

        $this->controller->index();

        Phake::verify($this->twig)->display(
            'posts/list.twig',
            [
                'posts' => $posts,
                'previousPage' => null,
                'nextPage' => null,
                'blogTitle' => null,
                'blogDesc' => null,
                'aboutText' => null,
                'externalLinks' => []
            ]
        );
    }

    public function testGetPosts(): void
    {
        $vars = ['page' => '2'];
        $posts = Phake::mock(Paginator::class);
        Phake::when($this->postService)->findPageOfLatestPosts(Phake::anyParameters())->thenReturn($posts);

        $this->controller->getPosts($vars);

        Phake::verify($this->twig)->display(
            'posts/list.twig',
            [
                'posts' => $posts,
                'previousPage' => null,
                'nextPage' => null,
                'blogTitle' => null,
                'blogDesc' => null,
                'aboutText' => null,
                'externalLinks' => []
            ]
        );
    }

    public function testGetPostsInvalidPage(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::NOT_FOUND);

        $this->controller->getPosts(['page' => 'test']);
    }

    public function testShowPost_IdNotFound(): void
    {
        Phake::when($this->postService)->getPost(self::POST_ID)->thenThrow(new PostNotFoundException());
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::NOT_FOUND);

        $this->controller->showPost(['id' => self::POST_ID]);
    }

    public function testShowPost(): void
    {
        $post = Phake::mock(Post::class);
        Phake::when($this->postService)->getPost(self::POST_ID)->thenReturn($post);

        $this->controller->showPost(['id' => self::POST_ID]);

        Phake::verify($this->twig)->display('posts/show.twig', ['post' => $post, 'blogTitle' => null]);
    }
}
