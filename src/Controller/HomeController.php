<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\PostViewingService;
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
    public function index()
    {
        $posts = $this->postViewingService->findPageOfLatestPosts();
        $this->twig->display('home.html.twig', ['posts' => $posts]);
    }
}