<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\CommentException;
use GabrielDeTassigny\Blog\Service\CommentService;
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

    public function __construct(
        AuthenticationService $authenticationService,
        Twig_Environment $twig,
        CommentService $commentService
    ) {
        $this->authenticationService = $authenticationService;
        $this->twig = $twig;
        $this->commentService = $commentService;
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
        $this->twig->display('comments/list.twig', ['comments' => $comments]);
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