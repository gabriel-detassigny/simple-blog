<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller\Admin;

use GabrielDeTassigny\Blog\Controller\Admin\ExternalLinkController;
use GabrielDeTassigny\Blog\Entity\ExternalLink;
use GabrielDeTassigny\Blog\Renderer\ErrorRenderer;
use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\AuthorService;
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
    private const AUTHOR_ID = 1;
    private const LINK_NAME = 'Facebook';
    private const LINK_URL = 'http://nsa.com/';
    private const FORM_PARAMS = ['link' => [
        'name' => self::LINK_NAME,
        'url' => self::LINK_URL,
        'author' => self::AUTHOR_ID
    ]];

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

    /** @var AuthorService|Phake_IMock */
    private $authorService;

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
        $this->authorService = Phake::mock(AuthorService::class);

        $this->controller = new ExternalLinkController(
            $this->authenticationService,
            $this->twig,
            $this->externalLinkService,
            $this->request,
            $this->errorRenderer,
            $this->authorService
        );
    }

    public function testNewExternalLink(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);

        $this->controller->newExternalLink(['authorId' => self::AUTHOR_ID]);

        Phake::verify($this->twig)->display('external-links/new.twig', ['author' => self::AUTHOR_ID]);
    }

    public function testNewExternalLink_ForbiddenAccess(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->newExternalLink(['authorId' => self::AUTHOR_ID]);
    }

    public function testCreateExternalLink(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()->thenReturn(self::FORM_PARAMS);

        $link = Phake::mock(ExternalLink::class);
        Phake::when($this->externalLinkService)->createExternalLink(self::LINK_NAME, self::LINK_URL)
            ->thenReturn($link);

        $this->controller->createExternalLink();

        Phake::verify($this->authorService)->addExternalLink(self::AUTHOR_ID, $link);
        Phake::verify($this->twig)->display(
            'external-links/new.twig',
            ['success' => self::SUCCESS_MESSAGE, 'author' => self::AUTHOR_ID]
        );
    }

    public function testCreateExternalLink_InvalidParams(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()->thenReturn([]);

        $this->controller->createExternalLink();
    }

    public function testCreateExternalLink_MissingMandatoryField(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()
            ->thenReturn(['link' => ['name' => self::LINK_NAME, 'url' => self::LINK_URL]]);

        $this->controller->createExternalLink();
    }

    public function testCreateExternalLink_ServiceException(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()->thenReturn(self::FORM_PARAMS);

        Phake::when($this->externalLinkService)->createExternalLink(self::LINK_NAME, self::LINK_URL)
            ->thenThrow(new ExternalLinkException('Creation failed!'));

        $this->controller->createExternalLink();

        Phake::verify($this->twig)->display(
            'external-links/new.twig',
            ['error' => 'Creation failed!', 'author' => self::AUTHOR_ID]
        );
    }

    public function testDeleteExternalLink(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);

        $link = Phake::mock(ExternalLink::class);
        Phake::when($this->externalLinkService)->getExternalLink(2)->thenReturn($link);

        $this->controller->deleteExternalLink(['id' => '2', 'authorId' => self::AUTHOR_ID]);

        Phake::verify($this->authorService)->removeExternalLink(self::AUTHOR_ID, $link);
        Phake::verify($this->errorRenderer, Phake::never())->renderError(Phake::anyParameters());
    }

    public function testDeleteExternalLink_ServiceException(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->externalLinkService)->getExternalLink(2)
            ->thenThrow(new ExternalLinkException('Link not found'));

        $this->controller->deleteExternalLink(['id' => '2', 'authorId' => self::AUTHOR_ID]);

        Phake::verify($this->errorRenderer)->setContentTypeToJson();
        Phake::verify($this->errorRenderer)->renderError(StatusCode::BAD_REQUEST, 'Link not found');
    }
}
