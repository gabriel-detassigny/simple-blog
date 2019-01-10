<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\CommentController;
use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use GabrielDeTassigny\Blog\Service\CommentException;
use GabrielDeTassigny\Blog\Service\CommentService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;

class CommentControllerTest extends TestCase
{
    /** @var CommentService|Phake_IMock */
    private $commentService;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /** @var CommentController */
    private $controller;

    /** @var JsonRenderer|Phake_IMock */
    private $jsonRenderer;

    protected function setUp()
    {
        $this->commentService = Phake::mock(CommentService::class);
        $this->request = Phake::mock(ServerRequestInterface::class);
        $this->jsonRenderer = Phake::mock(JsonRenderer::class);
        $this->controller = new CommentController($this->commentService, $this->request, $this->jsonRenderer);
    }

    public function testCreateComment_ServiceError(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->request)->getParsedBody()->thenReturn(['comment' => []]);
        Phake::when($this->commentService)->createUserComment([], 1)->thenThrow(new CommentException());

        $this->controller->createComment(['id' => 1]);
    }

    public function testCreateComment_InvalidParams(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->request)->getParsedBody()->thenReturn([]);

        $this->controller->createComment(['id' => 1]);
    }

    public function testCreateComment(): void
    {
        Phake::when($this->request)->getParsedBody()->thenReturn(['comment' => []]);

        $this->controller->createComment(['id' => 1]);

        Phake::verify($this->jsonRenderer)->render(['message' => 'Comment successfully created']);
    }
}
