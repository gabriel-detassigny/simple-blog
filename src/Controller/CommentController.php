<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use GabrielDeTassigny\Blog\Service\CaptchaService;
use GabrielDeTassigny\Blog\Service\Exception\CommentException;
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

    /** @var CaptchaService */
    private $captchaService;

    public function __construct(
        CommentService $commentService,
        ServerRequestInterface $request,
        JsonRenderer $jsonRenderer,
        CaptchaService $captchaService
    ) {
        $this->commentService = $commentService;
        $this->request = $request;
        $this->jsonRenderer = $jsonRenderer;
        $this->captchaService = $captchaService;
    }

    public function createComment(array $vars): void
    {
        $params = $this->getFormParams();

        if (!isset($params['captcha']) || !$this->captchaService->isValidCaptcha((string) $params['captcha'])) {
            throw new HttpException('Invalid captcha', StatusCode::BAD_REQUEST);
        }

        try {
            $this->commentService->createUserComment($params, (int) $vars['id']);
        } catch (CommentException $e) {
            throw new HttpException($e->getMessage(), StatusCode::BAD_REQUEST);
        }

        $this->jsonRenderer->render(['message' => 'Comment successfully created']);
    }

    public function refreshCaptcha(): void
    {
        $captcha = $this->captchaService->generateInlineCaptcha();

        $this->jsonRenderer->render(['captcha' => $captcha]);
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