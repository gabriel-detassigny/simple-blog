<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Renderer;

use GabrielDeTassigny\Blog\Renderer\ErrorRenderer;
use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Twig_Environment;

class ErrorRendererTest extends TestCase
{
    private const MESSAGE = 'Not Found';
    private const CODE = 404;

    /** @var ErrorRenderer */
    private $errorRenderer;

    /** @var JsonRenderer|Phake_IMock */
    private $jsonRenderer;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    public function setUp()
    {
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->jsonRenderer = Phake::mock(JsonRenderer::class);

        $this->errorRenderer = new ErrorRenderer($this->twig, $this->jsonRenderer);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRenderHtmlError()
    {
        $this->errorRenderer->renderError(self::CODE, self::MESSAGE);

        $this->assertSame(self::CODE, http_response_code());
        Phake::verify($this->twig)->display('error.twig', ['errorCode' => self::CODE, 'errorDescription' => self::MESSAGE]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRenderJsonError()
    {
        $this->errorRenderer->setContentTypeToJson();
        $this->errorRenderer->renderError(self::CODE, self::MESSAGE);

        $this->assertSame(self::CODE, http_response_code());
        Phake::verify($this->jsonRenderer)->render(['errorCode' => self::CODE, 'errorDescription' => self::MESSAGE]);
    }
}
