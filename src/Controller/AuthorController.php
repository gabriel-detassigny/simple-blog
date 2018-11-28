<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\AuthorService;
use Psr\Http\Message\ServerRequestInterface;
use Twig_Environment;

class AuthorController extends AdminController
{
    /** @var AuthorService */
    private $authorService;

    /** @var AuthenticationService */
    private $authenticationService;

    /** @var Twig_Environment */
    private $twig;

    /** @var ServerRequestInterface */
    private $request;

    public function __construct(
        AuthorService $authorService,
        AuthenticationService $authenticationService,
        Twig_Environment $twig,
        ServerRequestInterface $request
    ) {
        $this->authorService = $authorService;
        $this->authenticationService = $authenticationService;
        $this->twig = $twig;
        $this->request = $request;
    }

    public function newAuthor(): void
    {
        $this->ensureAdminAuthentication();
        $this->twig->display('authors/new.twig');
    }

    protected function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService;
    }
}