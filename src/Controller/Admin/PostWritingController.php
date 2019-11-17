<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller\Admin;

use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\Exception\PostWritingException;
use GabrielDeTassigny\Blog\Service\Exception\PostNotFoundException;
use GabrielDeTassigny\Blog\Service\Publishing\PostViewingService;
use GabrielDeTassigny\Blog\Service\Publishing\PostWritingService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;
use Twig_Error;

class PostWritingController extends AbstractAdminController
{
    private const POST_CREATION_SUCCESS = 'Post was successfully created';
    private const POST_UPDATING_SUCCESS = 'Post was successfully updated';

    /** @var Twig_Environment */
    private $twig;

    /** @var AdminAuthenticator */
    private $authenticationService;

    /** @var ServerRequestInterface */
    private $request;

    /** @var PostWritingService */
    private $postWritingService;

    /** @var AuthorService */
    private $authorService;

    /** @var PostViewingService */
    private $postViewingService;

    public function __construct(
        Twig_Environment $twig,
        AdminAuthenticator $authenticationService,
        ServerRequestInterface $request,
        PostWritingService $postWritingService,
        AuthorService $authorService,
        PostViewingService $postViewingService
    ) {
        $this->twig = $twig;
        $this->authenticationService = $authenticationService;
        $this->request = $request;
        $this->postWritingService = $postWritingService;
        $this->authorService = $authorService;
        $this->postViewingService = $postViewingService;
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

        $formParams = $this->getFormParams();

        try {
            $post = $this->postWritingService->createPost($formParams);
            $this->displayEditPostForm($post, ['success' => self::POST_CREATION_SUCCESS]);
        } catch (PostWritingException $e) {
            $this->displayNewPostForm(['error' => $e->getMessage(), 'post' => $formParams]);
        }
    }

    /**
     * @param array $vars
     * @throws HttpException
     * @throws Twig_Error
     * @return void
     */
    public function editPost(array $vars): void
    {
        $this->ensureAdminAuthentication();

        $this->displayEditPostForm($this->getPostFromId($vars), []);
    }

    /**
     * @param array $vars
     * @throws HttpException
     * @throws Twig_Error
     * @return void
     */
    public function updatePost(array $vars): void
    {
        $this->ensureAdminAuthentication();

        $post = $this->getPostFromId($vars);

        try {
            $this->postWritingService->updatePost($post, $this->getFormParams());
            $this->displayEditPostForm($post, ['success' => self::POST_UPDATING_SUCCESS]);
        } catch (PostWritingException $e) {
            $this->displayEditPostForm($post, ['error' => $e->getMessage()]);
        }
    }

    /**
     * @param array $vars
     * @throws HttpException
     * @throws Twig_Error
     * @return void
     */
    public function previewPost(array $vars): void
    {
        $this->ensureAdminAuthentication();

        $this->twig->display('posts/preview.twig', ['post' => $this->getPostFromId($vars)]);
    }

    protected function getAdminAuthenticator(): AdminAuthenticator
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

        $this->twig->display(
            'posts/new.twig',
            array_merge(['authors' => $authors], $params)
        );
    }

    /**
     * @param Post $post
     * @param array $params
     * @throws Twig_Error
     */
    private function displayEditPostForm(Post $post, array $params): void
    {
        $authors = $this->authorService->getAuthors();

        $this->twig->display(
            'posts/edit.twig',
            array_merge(['authors' => $authors, 'post' => $post], $params)
        );
    }

    /**
     * @param array $vars
     * @return Post
     * @throws HttpException
     */
    private function getPostFromId(array $vars): Post
    {
        try {
            return $this->postViewingService->getPost((int) $vars['id']);
        } catch (PostNotFoundException $e) {
            throw new HttpException('Could not find any post with ID ' . $vars['id'], StatusCode::NOT_FOUND);
        }
    }

    /**
     * @return array
     * @throws HttpException
     */
    private function getFormParams(): array
    {
        $body = $this->request->getParsedBody();

        if (!is_array($body) || !array_key_exists('post', $body) || !is_array($body['post'])) {
            throw new HttpException('Invalid form parameters', StatusCode::BAD_REQUEST);
        }

        return $body['post'];
    }
}
