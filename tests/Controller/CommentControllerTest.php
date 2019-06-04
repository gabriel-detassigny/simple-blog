<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\CommentController;
use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use GabrielDeTassigny\Blog\Service\CaptchaService;
use GabrielDeTassigny\Blog\Service\Exception\CommentException;
use GabrielDeTassigny\Blog\Service\CommentService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;

class CommentControllerTest extends TestCase
{
    private const CAPTCHA = 'ABC123';
    private const VALID_BODY = ['comment' => ['captcha' => self::CAPTCHA]];
    private const INLINE_IMG = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD';

    /** @var CommentService|Phake_IMock */
    private $commentService;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /** @var CommentController */
    private $controller;

    /** @var JsonRenderer|Phake_IMock */
    private $jsonRenderer;

    /** @var CaptchaService|Phake_IMock */
    private $captchaService;

    protected function setUp()
    {
        $this->commentService = Phake::mock(CommentService::class);
        $this->request = Phake::mock(ServerRequestInterface::class);
        $this->jsonRenderer = Phake::mock(JsonRenderer::class);
        $this->captchaService = Phake::mock(CaptchaService::class);
        $this->controller = new CommentController(
            $this->commentService,
            $this->request,
            $this->jsonRenderer,
            $this->captchaService
        );
    }

    public function testCreateComment_ServiceError(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->request)->getParsedBody()->thenReturn(self::VALID_BODY);
        Phake::when($this->captchaService)->isValidCaptcha(self::CAPTCHA)->thenReturn(true);
        Phake::when($this->commentService)->createUserComment(self::VALID_BODY['comment'], 1)
            ->thenThrow(new CommentException());

        $this->controller->createComment(['id' => 1]);
    }

    public function testCreateComment_InvalidParams(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->request)->getParsedBody()->thenReturn([]);

        $this->controller->createComment(['id' => 1]);
    }

    public function testCreateComment_InvalidCaptcha(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->request)->getParsedBody()->thenReturn(self::VALID_BODY);
        Phake::when($this->captchaService)->isValidCaptcha(self::CAPTCHA)->thenReturn(false);

        $this->controller->createComment(['id' => 1]);
    }

    public function testCreateComment(): void
    {
        Phake::when($this->request)->getParsedBody()->thenReturn(self::VALID_BODY);
        Phake::when($this->captchaService)->isValidCaptcha(self::CAPTCHA)->thenReturn(true);

        $this->controller->createComment(['id' => 1]);

        Phake::verify($this->jsonRenderer)->render(['message' => 'Comment successfully created']);
    }

    public function testRefreshCaptcha(): void
    {
        Phake::when($this->captchaService)->generateInlineCaptcha()->thenReturn(self::INLINE_IMG);

        $this->controller->refreshCaptcha();

        Phake::verify($this->jsonRenderer)->render(['captcha' => self::INLINE_IMG]);
    }
}
