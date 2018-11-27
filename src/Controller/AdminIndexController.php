<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use GabrielDeTassigny\Blog\ValueObject\Page;
use Teapot\HttpException;
use Twig_Environment;
use Twig_Error;

class AdminIndexController extends AdminController
{
    /** @var AuthenticationService */
    private $authenticationService;

    /** @var PostViewingService */
    private $postViewingService;

    /** @var AuthorService */
    private $authorService;

    /** @var Twig_Environment */
    private $twig;

    /** @var BlogInfoService */
    private $blogInfoService;

    public function __construct(
        Twig_Environment $twig,
        AuthenticationService $authenticationService,
        PostViewingService $postViewingService,
        AuthorService $authorService,
        BlogInfoService $blogInfoService
    ) {
        $this->authenticationService = $authenticationService;
        $this->postViewingService = $postViewingService;
        $this->authorService = $authorService;
        $this->twig = $twig;
        $this->blogInfoService = $blogInfoService;
    }

    /**
     * @throws Twig_Error
     * @throws HttpException
     */
    public function index(): void
    {
        $this->ensureAdminAuthentication();
        $posts = $this->postViewingService->findPageOfLatestPosts(new Page(1));
        $authors = $this->authorService->getAuthors();
        $blogTitle = $this->blogInfoService->getBlogTitle();
        $this->twig->display('admin.twig', ['posts' => $posts, 'authors' => $authors, 'blogTitle' => $blogTitle]);
    }

    protected function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService;
    }
}