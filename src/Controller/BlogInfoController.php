<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use Teapot\HttpException;
use Twig_Environment;
use Twig_Error;

class BlogInfoController extends AdminController
{
    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var Twig_Environment */
    private $twig;

    /** @var AuthenticationService */
    private $authenticationService;

    public function __construct(
        Twig_Environment $twig,
        BlogInfoService $blogInfoService,
        AuthenticationService $authenticationService
    ) {
        $this->twig = $twig;
        $this->blogInfoService = $blogInfoService;
        $this->authenticationService = $authenticationService;
    }

    /**
     * @throws HttpException
     * @throws Twig_Error
     */
    public function edit()
    {
        $this->ensureAdminAuthentication();
        $blogTitle = $this->blogInfoService->getBlogTitle();
        $blogDescription = $this->blogInfoService->getBlogDescription();
        $aboutText = $this->blogInfoService->getAboutText();

        $this->twig->display(
            'blog-info/edit.twig',
            ['blogTitle' => $blogTitle, 'blogDescription' => $blogDescription, 'aboutText' => $aboutText]
        );
    }

    protected function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService;
    }
}