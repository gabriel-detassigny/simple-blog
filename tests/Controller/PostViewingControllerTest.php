<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Controller\PostViewingController;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\CaptchaService;
use GabrielDeTassigny\Blog\Service\Exception\PostNotFoundException;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig\Environment;

class PostViewingControllerTest extends TestCase
{
    private const POST_ID = 1;
    private const CAPTCHA = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD';

    /** @var PostViewingController */
    private $controller;

    /** @var Environment|Phake_IMock */
    private $twig;

    /** @var PostViewingService|Phake_IMock */
    private $postService;

    /** @var BlogInfoService|Phake_IMock */
    private $blogInfoService;

    /** @var AuthorService|Phake_IMock */
    private $authorService;

    /** @var Author[] */
    private $authors;

    /** @var CaptchaService|Phake_IMock */
    private $captchaService;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->twig = Phake::mock(Environment::class);
        $this->postService = Phake::mock(PostViewingService::class);
        $this->blogInfoService = Phake::mock(BlogInfoService::class);
        $this->authorService = Phake::mock(AuthorService::class);
        $this->captchaService = Phake::mock(CaptchaService::class);

        $this->controller = new PostViewingController(
            $this->twig,
            $this->postService,
            $this->blogInfoService,
            $this->authorService,
            $this->captchaService
        );

        $this->authors = [Phake::mock(Author::class), Phake::mock(Author::class)];

        Phake::when($this->postService)->getPreviousPage(Phake::anyParameters())->thenReturn(null);
        Phake::when($this->postService)->getNextPage(Phake::anyParameters())->thenReturn(null);
        Phake::when($this->authorService)->getAuthors()->thenReturn($this->authors);
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
                'authors' => $this->authors
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
                'authors' => $this->authors
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
        Phake::when($this->captchaService)->generateInlineCaptcha()->thenReturn(self::CAPTCHA);

        $this->controller->showPost(['id' => self::POST_ID]);

        Phake::verify($this->twig)->display(
            'posts/show.twig',
            ['post' => $post, 'blogTitle' => null, 'captcha' => self::CAPTCHA]
        );
    }
}
