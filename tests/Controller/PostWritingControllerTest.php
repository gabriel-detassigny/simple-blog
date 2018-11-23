<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\PostWritingController;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\PostCreationException;
use GabrielDeTassigny\Blog\Service\PostWritingService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class PostWritingControllerTest extends TestCase
{
    private const BODY = ['post' => []];
    private const CREATION_ERROR = 'Error when creating post';

    /** @var PostWritingController */
    private $controller;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /** @var AuthenticationService|Phake_IMock */
    private $authenticationService;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /** @var PostWritingService|Phake_IMock */
    private $postWritingService;

    /** @var AuthorService|Phake_IMock */
    private $authorService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->authenticationService = Phake::mock(AuthenticationService::class);
        $this->request = Phake::mock(ServerRequestInterface::class);
        Phake::when($this->request)->getParsedBody()->thenReturn([]);
        $this->postWritingService = Phake::mock(PostWritingService::class);
        $this->authorService = Phake::mock(AuthorService::class);

        $this->controller = new PostWritingController(
            $this->twig,
            $this->authenticationService,
            $this->request,
            $this->postWritingService,
            $this->authorService
        );
    }

    public function testNewPost()
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->authorService)->getAuthors()->thenReturn([]);

        $this->controller->newPost();

        Phake::verify($this->twig)->display('posts/new.twig', ['authors' => []]);
    }

    public function testNewPost_ForbiddenAccess()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->newPost();
    }

    public function testCreatePost_ForbiddenAccess()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->createPost();
    }

    public function testCreatePost_InvalidParameters()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);

        $this->controller->createPost();
    }

    public function testCreatePost_CreationError()
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->postWritingService)->createPost(self::BODY['post'])
            ->thenThrow(new PostCreationException(self::CREATION_ERROR));
        Phake::when($this->request)->getParsedBody()->thenReturn(self::BODY);
        Phake::when($this->authorService)->getAuthors()->thenReturn([]);

        $this->controller->createPost();

        Phake::verify($this->twig)->display('posts/new.twig', ['error' => self::CREATION_ERROR, 'authors' => []]);
    }

    public function testCreatePost()
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        Phake::when($this->request)->getParsedBody()->thenReturn(self::BODY);

        $this->controller->createPost();

        Phake::verify($this->twig)->display('admin.twig', Phake::ignoreRemaining());
    }

    public function testIndex()
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        $this->controller->index();
        Phake::verify($this->twig)->display('admin.twig');
    }

    public function testIndex_ForbiddenAccess()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->index();
    }
}
