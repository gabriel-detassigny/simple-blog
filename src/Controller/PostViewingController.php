<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\PostNotFoundException;
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

    /** @var BlogInfoService */
    private $blogInfoService;

    public function __construct(Twig_Environment $twig, PostViewingService $postViewingService, BlogInfoService $blogInfoService)
    {
        $this->twig = $twig;
        $this->postViewingService = $postViewingService;
        $this->blogInfoService = $blogInfoService;
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
        $blogTitle = $this->blogInfoService->getBlogTitle();
        $blogDesc = $this->blogInfoService->getBlogDescription();

        $this->twig->display(
            'posts/list.twig',
            [
                'posts' => $posts,
                'previousPage' => $previousPage,
                'nextPage' => $nextPage,
                'blogTitle' => $blogTitle,
                'blogDesc' => $blogDesc
            ]
        );
    }

    /**
     * @param array $vars
     * @throws HttpException
     * @throws Twig_Error
     */
    public function showPost(array $vars): void
    {
        try {
            $post = $this->postViewingService->getPost((int) $vars['id']);
        } catch (PostNotFoundException $e) {
            throw new HttpException('Post not found', StatusCode::NOT_FOUND);
        }
        $this->twig->display('posts/show.twig', ['post' => $post]);
    }
}