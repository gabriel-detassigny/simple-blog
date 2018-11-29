<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
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

    /** @var ExternalLinkService */
    private $externalLinkService;

    public function __construct(
        Twig_Environment $twig,
        AuthenticationService $authenticationService,
        PostViewingService $postViewingService,
        AuthorService $authorService,
        BlogInfoService $blogInfoService,
        ExternalLinkService $externalLinkService
    ) {
        $this->authenticationService = $authenticationService;
        $this->postViewingService = $postViewingService;
        $this->authorService = $authorService;
        $this->twig = $twig;
        $this->blogInfoService = $blogInfoService;
        $this->externalLinkService = $externalLinkService;
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
        $externalLinks = $this->externalLinkService->getExternalLinks();
        $this->twig->display(
            'admin-index.twig',
            ['posts' => $posts, 'authors' => $authors, 'title' => $blogTitle, 'externalLinks' => $externalLinks]
        );
    }

    protected function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService;
    }
}