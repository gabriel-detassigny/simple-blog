<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller\Admin;

use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use GabrielDeTassigny\Blog\Service\Publishing\PostViewingService;
use Teapot\HttpException;
use Twig\Environment;
use Twig\Error\Error;

class AdminIndexController extends AbstractAdminController
{
    /** @var AdminAuthenticator */
    private $authenticationService;

    /** @var PostViewingService */
    private $postViewingService;

    /** @var AuthorService */
    private $authorService;

    /** @var Environment */
    private $twig;

    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var ExternalLinkService */
    private $externalLinkService;

    public function __construct(
        Environment $twig,
        AdminAuthenticator $authenticationService,
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
     * @throws Error
     * @throws HttpException
     */
    public function index(): void
    {
        $this->ensureAdminAuthentication();

        $this->twig->display('admin-index.twig', [
            'posts' => $this->postViewingService->findLatestPublishedPosts(),
            'drafts' => $this->postViewingService->findLatestDraftPosts(),
            'authors' => $this->authorService->getAuthors(),
            'title' => $this->blogInfoService->getBlogTitle(),
            'externalLinks' => $this->externalLinkService->getExternalLinks()
        ]);
    }

    protected function getAdminAuthenticator(): AdminAuthenticator
    {
        return $this->authenticationService;
    }
}