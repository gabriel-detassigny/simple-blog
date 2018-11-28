<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\AuthorException;
use GabrielDeTassigny\Blog\Service\AuthorService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class AuthorController extends AdminController
{
    private const SUCCESS_MESSAGE = 'Author successfully created';

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

    public function createAuthor(): void
    {
        $this->ensureAdminAuthentication();
        $body = $this->request->getParsedBody();
        if (!is_array($body) || !array_key_exists('author', $body) || !is_array($body['author'])) {
            throw new HttpException('Invalid form parameters', StatusCode::BAD_REQUEST);
        }
        try {
            $this->authorService->createAuthor($body['author']['name']);
            $this->twig->display('authors/new.twig', ['success' => self::SUCCESS_MESSAGE]);
        } catch (AuthorException $e) {
            $this->twig->display('authors/new.twig', ['error' => $e->getMessage()]);
        }
    }

    protected function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService;
    }
}