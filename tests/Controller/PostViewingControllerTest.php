<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Controller\PostViewingController;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class PostViewingControllerTest extends TestCase
{
    /** @var PostViewingController */
    private $controller;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /** @var PostViewingService|Phake_IMock */
    private $postService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->postService = Phake::mock(PostViewingService::class);
        $this->controller = new PostViewingController($this->twig, $this->postService);

        Phake::when($this->postService)->getPreviousPage(Phake::anyParameters())->thenReturn(null);
        Phake::when($this->postService)->getNextPage(Phake::anyParameters())->thenReturn(null);
    }

    public function testIndexWillDisplayTwigView(): void
    {
        $posts = Phake::mock(Paginator::class);
        Phake::when($this->postService)->findPageOfLatestPosts(Phake::anyParameters())->thenReturn($posts);

        $this->controller->index();

        Phake::verify($this->twig)->display(
            'home.html.twig',
            ['posts' => $posts, 'previousPage' => null, 'nextPage' => null]
        );
    }

    public function testGetPosts(): void
    {
        $vars = ['page' => '2'];
        $posts = Phake::mock(Paginator::class);
        Phake::when($this->postService)->findPageOfLatestPosts(Phake::anyParameters())->thenReturn($posts);

        $this->controller->getPosts($vars);

        Phake::verify($this->twig)->display(
            'home.html.twig',
            ['posts' => $posts, 'previousPage' => null, 'nextPage' => null]
        );
    }

    public function testGetPostsInvalidPage(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::NOT_FOUND);

        $this->controller->getPosts(['page' => 'test']);
    }
}
