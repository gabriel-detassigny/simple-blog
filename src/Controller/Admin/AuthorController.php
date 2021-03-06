<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller\Admin;

use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\Exception\AuthorException;
use GabrielDeTassigny\Blog\Service\AuthorService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig\Environment;

class AuthorController extends AbstractAdminController
{
    private const SUCCESS_MESSAGE = 'Author successfully created';

    /** @var AuthorService */
    private $authorService;

    /** @var AdminAuthenticator */
    private $authenticationService;

    /** @var Environment */
    private $twig;

    /** @var ServerRequestInterface */
    private $request;

    public function __construct(
        AuthorService $authorService,
        AdminAuthenticator $authenticationService,
        Environment $twig,
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

    protected function getAdminAuthenticator(): AdminAuthenticator
    {
        return $this->authenticationService;
    }
}