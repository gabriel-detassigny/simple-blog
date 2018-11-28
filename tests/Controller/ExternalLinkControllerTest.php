<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\ExternalLinkController;
use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class ExternalLinkControllerTest extends TestCase
{
    /** @var AuthenticationService|Phake_IMock */
    private $authenticationService;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /** @var JsonRenderer|Phake_IMock */
    private $renderer;

    /** @var ExternalLinkService|Phake_IMock */
    private $externalLinkService;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /** @var ExternalLinkController */
    private $controller;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->authenticationService = Phake::mock(AuthenticationService::class);
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->renderer = Phake::mock(JsonRenderer::class);
        $this->externalLinkService = Phake::mock(ExternalLinkService::class);
        $this->request = Phake::mock(ServerRequestInterface::class);

        $this->controller = new ExternalLinkController(
            $this->authenticationService,
            $this->twig,
            $this->renderer,
            $this->externalLinkService,
            $this->request
        );
    }

    public function testNewExternalLink()
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);

        $this->controller->newExternalLink();

        Phake::verify($this->twig)->display('external-links/new.twig');
    }

    public function testNewExternalLink_ForbiddenAccess()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->newExternalLink();
    }
}
