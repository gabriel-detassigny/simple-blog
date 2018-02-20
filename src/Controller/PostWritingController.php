<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthenticationService;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;
use Twig_Error;

class PostWritingController
{
    /** @var Twig_Environment */
    private $twig;

    /** @var AuthenticationService */
    private $authenticationService;

    public function __construct(Twig_Environment $twig, AuthenticationService $authenticationService)
    {
        $this->twig = $twig;
        $this->authenticationService = $authenticationService;
    }

    /**
     * @throws Twig_Error
     * @throws HttpException
     */
    public function newPost()
    {
        if (!$this->authenticationService->authenticateAsAdmin()) {
            throw new HttpException('Authentication failed', StatusCode::FORBIDDEN);
        }
        $this->twig->display('posts/new.twig');
    }
}