<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Repository\PostRepository;
use Twig_Environment;

class HomeController
{
    /** @var Twig_Environment */
    private $twig;

    /** @var PostRepository */
    private $postRepository;

    public function __construct(Twig_Environment $twig, PostRepository $postRepository)
    {
        $this->twig = $twig;
        $this->postRepository = $postRepository;
    }

    public function index()
    {
        $posts = $this->postRepository->findAll();
        $this->twig->display('home.html.twig', ['posts' => $posts]);
    }
}