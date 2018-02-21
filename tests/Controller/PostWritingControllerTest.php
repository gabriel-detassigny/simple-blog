<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\PostWritingController;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class PostWritingControllerTest extends TestCase
{
    /** @var PostWritingController */
    private $controller;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /** @var AuthenticationService|Phake_IMock */
    private $authenticationService;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->authenticationService = Phake::mock(AuthenticationService::class);
        $this->request = Phake::mock(ServerRequestInterface::class);
        Phake::when($this->request)->getParsedBody()->thenReturn([]);
        $this->controller = new PostWritingController($this->twig, $this->authenticationService, $this->request);
    }

    public function testNewPost()
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        $this->controller->newPost();

        Phake::verify($this->twig)->display('posts/new.twig');
    }

    public function testNewPost_ForbiddenAccess()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::FORBIDDEN);

        $this->controller->newPost();
    }

    public function testCreatePost_ForbiddenAccess()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::FORBIDDEN);

        $this->controller->createPost();
    }

    public function testCreatePost_InvalidParameters()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);

        $this->controller->createPost();
    }
}
