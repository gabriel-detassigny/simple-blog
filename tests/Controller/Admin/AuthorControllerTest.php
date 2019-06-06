<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller\Admin;

use GabrielDeTassigny\Blog\Controller\Admin\AuthorController;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\Exception\AuthorException;
use GabrielDeTassigny\Blog\Service\AuthorService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class AuthorControllerTest extends TestCase
{
    private const SUCCESS_MESSAGE = 'Author successfully created';

    /** @var AuthorService|Phake_IMock */
    private $authorService;

    /** @var AuthenticationService|Phake_IMock */
    private $authenticationService;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /** @var AuthorController */
    private $controller;

    protected function setUp()
    {
        parent::setUp();
        $this->authorService = Phake::mock(AuthorService::class);
        $this->authenticationService = Phake::mock(AuthenticationService::class);
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->request = Phake::mock(ServerRequestInterface::class);
        $this->controller = new AuthorController(
            $this->authorService,
            $this->authenticationService,
            $this->twig,
            $this->request
        );
    }

    public function testNewAuthor(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);

        $this->controller->newAuthor();

        Phake::verify($this->twig)->display('authors/new.twig');
    }

    public function testNewAuthor_Unauthorized(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->newAuthor();
    }

    public function testCreateAuthor(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()->thenReturn(['author' => ['name' => 'Stephen King']]);

        $this->controller->createAuthor();

        Phake::verify($this->twig)->display('authors/new.twig', ['success' => self::SUCCESS_MESSAGE]);
    }

    public function testCreateAuthor_InvalidParams(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()->thenReturn([]);

        $this->controller->createAuthor();
    }

    public function testCreateAuthor_ServiceError(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()->thenReturn(['author' => ['name' => 'Stephen King']]);
        Phake::when($this->authorService)->createAuthor('Stephen King')->thenThrow(new AuthorException('Error!'));

        $this->controller->createAuthor();

        Phake::verify($this->twig)->display('authors/new.twig', ['error' => 'Error!']);
    }
}
