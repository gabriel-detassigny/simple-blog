<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller\Admin;

use GabrielDeTassigny\Blog\Controller\Admin\AbstractAdminController;
use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\Exception\CommentException;
use GabrielDeTassigny\Blog\Service\CommentService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;
use Twig_Error;

class CommentAdminController extends AbstractAdminController
{
    /** @var AdminAuthenticator */
    private $authenticationService;

    /** @var Twig_Environment */
    private $twig;

    /** @var CommentService */
    private $commentService;

    /** @var ServerRequestInterface */
    private $request;

    public function __construct(
        AdminAuthenticator $authenticationService,
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

        $this->twig->display('comments/new.twig', ['postId' => (int) $vars['id']]);
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

        try {
            $this->commentService->deleteComment((int) $vars['id']);
        } catch (CommentException $e) {
            throw new HttpException($e->getMessage(), StatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    protected function getAdminAuthenticator(): AdminAuthenticator
    {
        return $this->authenticationService;
    }
}