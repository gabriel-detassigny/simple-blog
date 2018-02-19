<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\PostViewingService;
use GabrielDeTassigny\Blog\ValueObject\InvalidPageException;
use GabrielDeTassigny\Blog\ValueObject\Page;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;
use Twig_Error;

class PostViewingController
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
     * @throws Twig_Error
     * @throws HttpException
     */
    public function index(): void
    {
        $this->getPosts(['page' => 1]);
    }

    /**
     * @param array $vars
     * @throws Twig_Error
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
        $previousPage = $this->postViewingService->getPreviousPage($page);
        $nextPage = $this->postViewingService->getNextPage($page, count($posts));

        $params = ['posts' => $posts, 'previousPage' => $previousPage, 'nextPage' => $nextPage];
        $this->twig->display('home.html.twig', $params);
    }
}