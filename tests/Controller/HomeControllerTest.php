<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\HomeController;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\Service\PostViewingService;
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

    /** @var PostViewingService|Phake_IMock */
    private $postService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->postService = Phake::mock(PostViewingService::class);
        $this->controller = new HomeController($this->twig, $this->postService);
    }

    public function testIndexWillDisplayTwigView()
    {
        $posts = [Phake::mock(Post::class), Phake::mock(Post::class)];
        Phake::when($this->postService)->findPageOfLatestPosts()->thenReturn($posts);

        $this->controller->index();

        Phake::verify($this->twig)->display('home.html.twig', ['posts' => $posts]);
    }
}
