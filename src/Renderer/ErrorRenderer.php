<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Renderer;

use Twig\Environment;

class ErrorRenderer
{
    public const JSON = 'application/json';
    public const HTML = 'text/html';

    /** @var Environment */
    private $twig;

    /** @var JsonRenderer */
    private $jsonRenderer;

    /** @var string */
    private $contentType;

    public function __construct(Environment $twig, JsonRenderer $jsonRenderer)
    {
        $this->twig = $twig;
        $this->jsonRenderer = $jsonRenderer;
        $this->contentType = self::HTML;
    }

    public function setContentTypeToJson(): void
    {
        $this->contentType = self::JSON;
    }

    public function renderError(int $errorCode, string $errorMessage): void
    {
        header('Content-Type: ' . $this->contentType . '; charset=UTF-8');
        http_response_code($errorCode);
        switch ($this->contentType) {
            case self::HTML:
                $this->twig->display('error.twig', ['errorCode' => $errorCode, 'errorDescription' => $errorMessage]);
                break;
            case self::JSON:
                $this->jsonRenderer->render(['errorCode' => $errorCode, 'errorDescription' => $errorMessage]);
                break;
        }
    }
}