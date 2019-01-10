<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
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

    /** @var JsonRenderer */
    private $jsonRenderer;

    public function __construct(
        CommentService $commentService,
        ServerRequestInterface $request,
        JsonRenderer $jsonRenderer
    ) {
        $this->commentService = $commentService;
        $this->request = $request;
        $this->jsonRenderer = $jsonRenderer;
    }

    public function createComment(array $vars): void
    {
        $postId = (int) $vars['id'];
        $params = $this->getFormParams();
        try {
            $this->commentService->createUserComment($params, $postId);
        } catch (CommentException $e) {
            throw new HttpException($e->getMessage(), StatusCode::BAD_REQUEST);
        }
        $this->jsonRenderer->render(['message' => 'Comment successfully created']);
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