<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\Exception\CommentException;
use GabrielDeTassigny\Blog\Service\CommentService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;
use Twig_Error;

class CommentAdminController extends AdminController
{
    /** @var AuthenticationService */
    private $authenticationService;

    /** @var Twig_Environment */
    private $twig;

    /** @var CommentService */
    private $commentService;

    /** @var ServerRequestInterface */
    private $request;

    public function __construct(
        AuthenticationService $authenticationService,
        Twig_Environment $twig,
        CommentService $commentService,
        ServerRequestInterface $request
    ) {
        $this->authenticationService = $authenticationService;
        $this->twig = $twig;
        $this->commentService = $commentService;
        $this->request = $request;
    }

    /**
     * @param array $vars
     * @throws HttpException
     * @throws Twig_Error
     */
    public function index(array $vars): void
    {
        $this->ensureAdminAuthentication();
        $postId = (int) $vars['id'];
        try {
            $comments = $this->commentService->getPostComments($postId);
        } catch (CommentException $e) {
            throw new HttpException($e->getMessage(), StatusCode::INTERNAL_SERVER_ERROR);
        }
        $this->twig->display('comments/list.twig', ['comments' => $comments, 'postId' => $postId]);
    }

    public function newComment(array $vars): void
    {
        $this->ensureAdminAuthentication();
        $postId = (int) $vars['id'];
        $this->twig->display('comments/new.twig', ['postId' => $postId]);
    }

    public function createComment(array $vars): void
    {
        $this->ensureAdminAuthentication();
        $postId = (int) $vars['id'];
        $body = $this->request->getParsedBody();
        if (!is_array($body) || !array_key_exists('comment', $body) || !is_array($body['comment'])) {
            throw new HttpException('Invalid form parameters', StatusCode::BAD_REQUEST);
        }
        try {
            $this->commentService->createAdminComment($body['comment']['text'], $postId);
            $this->twig->display('comments/new.twig', ['success' => 'Comment added!', 'postId' => $postId]);
        } catch (CommentException $e) {
            $this->twig->display('comments/new.twig', ['error' => $e->getMessage(), 'postId' => $postId]);
        }
    }

    public function deleteComment(array $vars): void
    {
        $this->ensureAdminAuthentication();
        $commentId = (int) $vars['id'];

        try {
            $this->commentService->deleteComment($commentId);
        } catch (CommentException $e) {
            throw new HttpException($e->getMessage(), StatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    protected function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService;
    }
}