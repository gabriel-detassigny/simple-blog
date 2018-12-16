<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\CommentException;
use GabrielDeTassigny\Blog\Service\CommentService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;

class CommentController
{
    /** @var CommentService */
    private $commentService;

    /** @var ServerRequestInterface */
    private $request;

    public function __construct(
        CommentService $commentService,
        ServerRequestInterface $request
    ) {
        $this->commentService = $commentService;
        $this->request = $request;
    }

    public function createComment(array $vars): void
    {
        $postId = (int) $vars['id'];
        $params = $this->getFormParams();
        try {
            $this->commentService->createComment($params, $postId);
        } catch (CommentException $e) {
            throw new HttpException($e->getMessage(), StatusCode::BAD_REQUEST);
        }
    }

    /**
     * @return array
     * @throws HttpException
     */
    private function getFormParams(): array
    {
        $body = $this->request->getParsedBody();
        if (!is_array($body) || !array_key_exists('comment', $body) || !is_array($body['comment'])) {
            throw new HttpException('Invalid form parameters', StatusCode::BAD_REQUEST);
        }
        return $body['comment'];
    }
}