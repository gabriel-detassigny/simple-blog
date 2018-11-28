<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\AuthorController;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
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

    public function testNewPost()
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);

        $this->controller->newAuthor();

        Phake::verify($this->twig)->display('authors/new.twig');
    }

    public function testNewPost_Unauthorized()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->newAuthor();
    }
}
