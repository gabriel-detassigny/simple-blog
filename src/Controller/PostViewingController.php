<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\CaptchaService;
use GabrielDeTassigny\Blog\Service\Exception\PostNotFoundException;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use GabrielDeTassigny\Blog\ValueObject\InvalidPageException;
use GabrielDeTassigny\Blog\ValueObject\Page;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig\Environment;
use Twig\Error\Error;

class PostViewingController
{
    /** @var Environment */
    private $twig;

    /** @var PostViewingService */
    private $postViewingService;

    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var AuthorService */
    private $authorService;

    /** @var CaptchaService */
    private $captchaService;

    public function __construct(
        Environment $twig,
        PostViewingService $postViewingService,
        BlogInfoService $blogInfoService,
        AuthorService $authorService,
        CaptchaService $captchaService
    ) {
        $this->twig = $twig;
        $this->postViewingService = $postViewingService;
        $this->blogInfoService = $blogInfoService;
        $this->authorService = $authorService;
        $this->captchaService = $captchaService;
    }

    /**
     * @throws HttpException
     */
    public function index(): void
    {
        $this->getPosts(['page' => 1]);
    }

    /**
     * @param array $vars
     * @throws HttpException
     * @throws Error
     */
    public function getPosts(array $vars): void
    {
        try {
            $page = new Page((int) $vars['page']);
        } catch (InvalidPageException $e) {
            throw new HttpException('Page not found', StatusCode::NOT_FOUND);
        }

        $posts = $this->postViewingService->findPageOfLatestPosts($page);

        $this->twig->display(
            'posts/list.twig',
            [
                'posts' => $posts,
                'previousPage' => $this->postViewingService->getPreviousPage($page),
                'nextPage' => $this->postViewingService->getNextPage($page, count($posts)),
                'blogTitle' => $this->blogInfoService->getBlogTitle(),
                'blogDesc' => $this->blogInfoService->getBlogDescription(),
                'aboutText' => $this->blogInfoService->getAboutText(),
                'authors' => $this->authorService->getAuthors()
            ]
        );
    }

    /**
     * @param array $vars
     * @throws HttpException
     * @throws Error
     */
    public function showPost(array $vars): void
    {
        try {
            $post = $this->postViewingService->getPost((int) $vars['id']);
        } catch (PostNotFoundException $e) {
            throw new HttpException('Post not found', StatusCode::NOT_FOUND);
        }

        $this->twig->display('posts/show.twig', [
            'post' => $post,
            'blogTitle' => $this->blogInfoService->getBlogTitle(),
            'captcha' => $this->captchaService->generateInlineCaptcha()
        ]);
    }
}