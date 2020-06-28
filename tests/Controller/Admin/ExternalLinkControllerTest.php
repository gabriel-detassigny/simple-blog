<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller\Admin;

use GabrielDeTassigny\Blog\Controller\Admin\ExternalLinkController;
use GabrielDeTassigny\Blog\Renderer\ErrorRenderer;
use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\Exception\ExternalLinkException;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig\Environment;

class ExternalLinkControllerTest extends TestCase
{
    private const SUCCESS_MESSAGE = 'External Link was successfully created!';

    /** @var AdminAuthenticator|Phake_IMock */
    private $authenticationService;

    /** @var Environment|Phake_IMock */
    private $twig;

    /** @var ExternalLinkService|Phake_IMock */
    private $externalLinkService;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /** @var ErrorRenderer|Phake_IMock */
    private $errorRenderer;

    /** @var ExternalLinkController */
    private $controller;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->authenticationService = Phake::mock(AdminAuthenticator::class);
        $this->twig = Phake::mock(Environment::class);
        $this->externalLinkService = Phake::mock(ExternalLinkService::class);
        $this->request = Phake::mock(ServerRequestInterface::class);
        $this->errorRenderer = Phake::mock(ErrorRenderer::class);

        $this->controller = new ExternalLinkController(
            $this->authenticationService,
            $this->twig,
            $this->externalLinkService,
            $this->request,
            $this->errorRenderer
        );
    }

    public function testNewExternalLink(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);

        $this->controller->newExternalLink();

        Phake::verify($this->twig)->display('external-links/new.twig');
    }

    public function testNewExternalLink_ForbiddenAccess(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->newExternalLink();
    }

    public function testCreateExternalLink(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()
            ->thenReturn(['link' => ['name' => 'Facebook', 'url' => 'http://nsa.com/']]);

        $this->controller->createExternalLink();

        Phake::verify($this->twig)->display('external-links/new.twig', ['success' => self::SUCCESS_MESSAGE]);
    }

    public function testCreateExternalLink_InvalidParams(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()->thenReturn([]);

        $this->controller->createExternalLink();
    }

    public function testCreateExternalLink_ServiceException(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()
            ->thenReturn(['link' => ['name' => 'Facebook', 'url' => 'http://nsa.com/']]);
        Phake::when($this->externalLinkService)->createExternalLink('Facebook', 'http://nsa.com/')
            ->thenThrow(new ExternalLinkException('Creation failed!'));

        $this->controller->createExternalLink();

        Phake::verify($this->twig)->display('external-links/new.twig', ['error' => 'Creation failed!']);
    }

    public function testDeleteExternalLink(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);

        $this->controller->deleteExternalLink(['id' => '1']);

        Phake::verify($this->externalLinkService)->deleteExternalLink(1);
        Phake::verify($this->errorRenderer, Phake::never())->renderError(Phake::anyParameters());
    }

    public function testDeleteExternalLink_ServiceException(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->externalLinkService)->deleteExternalLink(1)
            ->thenThrow(new ExternalLinkException('Error deleting'));

        $this->controller->deleteExternalLink(['id' => '1']);

        Phake::verify($this->errorRenderer)->setContentTypeToJson();
        Phake::verify($this->errorRenderer)->renderError(StatusCode::BAD_REQUEST, 'Error deleting');
    }
}
