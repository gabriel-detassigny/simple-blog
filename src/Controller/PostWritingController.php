<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\PostCreationException;
use GabrielDeTassigny\Blog\Service\PostWritingService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;
use Twig_Error;

class PostWritingController extends AdminController
{
    /** @var Twig_Environment */
    private $twig;

    /** @var AuthenticationService */
    private $authenticationService;

    /** @var ServerRequestInterface */
    private $request;

    /** @var PostWritingService */
    private $postWritingService;

    public function __construct(
        Twig_Environment $twig,
        AuthenticationService $authenticationService,
        ServerRequestInterface $request,
        PostWritingService $postWritingService
    ) {
        $this->twig = $twig;
        $this->authenticationService = $authenticationService;
        $this->request = $request;
        $this->postWritingService = $postWritingService;
    }

    /**
     * @throws Twig_Error
     * @throws HttpException
     */
    public function index(): void
    {
        $this->ensureAdminAuthentication();
        $this->twig->display('admin.twig');
    }

    /**
     * @throws Twig_Error
     * @throws HttpException
     */
    public function newPost(): void
    {
        $this->ensureAdminAuthentication();
        $this->twig->display('posts/new.twig');
    }

    /**
     * @throws HttpException
     * @throws Twig_Error
     */
    public function createPost(): void
    {
        $this->ensureAdminAuthentication();
        $body = $this->request->getParsedBody();
        if (!array_key_exists('post', $body) || !is_array($body['post'])) {
            throw new HttpException('Invalid form parameters', StatusCode::BAD_REQUEST);
        }
        try {
            $this->postWritingService->createPost($body['post']);
        } catch (PostCreationException $e) {
            $this->twig->display('posts/new.twig', ['error' => $e->getMessage()]);
            return;
        }
        $this->twig->display('admin.twig', ['success' => 'Post successfully created']);
    }

    protected function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService;
    }
}
