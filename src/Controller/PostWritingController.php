<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\PostCreationException;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use GabrielDeTassigny\Blog\Service\PostWritingService;
use GabrielDeTassigny\Blog\ValueObject\Page;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;
use Twig_Error;

class PostWritingController extends AdminController
{
    private const POST_CREATION_SUCCESS = 'Post was successfully created';

    /** @var Twig_Environment */
    private $twig;

    /** @var AuthenticationService */
    private $authenticationService;

    /** @var ServerRequestInterface */
    private $request;

    /** @var PostWritingService */
    private $postWritingService;

    /** @var AuthorService */
    private $authorService;

    public function __construct(
        Twig_Environment $twig,
        AuthenticationService $authenticationService,
        ServerRequestInterface $request,
        PostWritingService $postWritingService,
        AuthorService $authorService
    ) {
        $this->twig = $twig;
        $this->authenticationService = $authenticationService;
        $this->request = $request;
        $this->postWritingService = $postWritingService;
        $this->authorService = $authorService;
    }

    /**
     * @throws Twig_Error
     * @throws HttpException
     */
    public function newPost(): void
    {
        $this->ensureAdminAuthentication();
        $this->displayNewPostForm([]);
    }

    /**
     * @throws HttpException
     * @throws Twig_Error
     */
    public function createPost(): void
    {
        $this->ensureAdminAuthentication();
        $body = $this->request->getParsedBody();
        if (!is_array($body) || !array_key_exists('post', $body) || !is_array($body['post'])) {
            throw new HttpException('Invalid form parameters', StatusCode::BAD_REQUEST);
        }
        try {
            $this->postWritingService->createPost($body['post']);
            $this->displayNewPostForm(['success' => self::POST_CREATION_SUCCESS]);
        } catch (PostCreationException $e) {
            $this->displayNewPostForm(['error' => $e->getMessage()]);
        }
    }

    protected function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService;
    }

    /**
     * @param array $params
     * @return void
     * @throws Twig_Error
     */
    private function displayNewPostForm(array $params): void
    {
        $authors = $this->authorService->getAuthors();
        $this->twig->display('posts/new.twig', array_merge(['authors' => $authors], $params));
    }
}
