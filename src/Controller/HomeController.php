<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\PostViewingService;
use GabrielDeTassigny\Blog\ValueObject\InvalidPageException;
use GabrielDeTassigny\Blog\ValueObject\Page;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class HomeController
{
    /** @var Twig_Environment */
    private $twig;

    /** @var PostViewingService */
    private $postViewingService;

    public function __construct(Twig_Environment $twig, PostViewingService $postViewingService)
    {
        $this->twig = $twig;
        $this->postViewingService = $postViewingService;
    }

    /**
     * @throws \Twig_Error
     */
    public function index(): void
    {
        $posts = $this->postViewingService->findPageOfLatestPosts(new Page(1));
        $this->twig->display('home.html.twig', ['posts' => $posts]);
    }

    /**
     * @param array $vars
     * @throws \Twig_Error
     * @throws HttpException
     */
    public function getPosts(array $vars): void
    {
        try {
            $page = new Page((int) $vars['page']);
        } catch (InvalidPageException $e) {
            throw new HttpException('Page not found', StatusCode::NOT_FOUND);
        }

        $posts = $this->postViewingService->findPageOfLatestPosts($page);
        $this->twig->display('home.html.twig', ['posts' => $posts]);
    }
}