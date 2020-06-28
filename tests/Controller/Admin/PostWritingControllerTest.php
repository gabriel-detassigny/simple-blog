<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller\Admin;

use GabrielDeTassigny\Blog\Controller\Admin\PostWritingController;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\Exception\PostWritingException;
use GabrielDeTassigny\Blog\Service\Exception\PostNotFoundException;
use GabrielDeTassigny\Blog\Service\Publishing\PostViewingService;
use GabrielDeTassigny\Blog\Service\Publishing\PostWritingService;
use GabrielDeTassigny\Blog\ValueObject\CommentType;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig\Environment;

class PostWritingControllerTest extends TestCase
{
    private const BODY = ['post' => []];
    private const CREATION_ERROR = 'Error when creating post';
    private const POST_CREATION_SUCCESS = 'Post was successfully created';
    private const POST_UPDATING_SUCCESS = 'Post was successfully updated';
    private const ID = 1;

    /** @var PostWritingController */
    private $controller;

    /** @var Environment|Phake_IMock */
    private $twig;

    /** @var AdminAuthenticator|Phake_IMock */
    private $authenticationService;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /** @var PostWritingService|Phake_IMock */
    private $postWritingService;

    /** @var AuthorService|Phake_IMock */
    private $authorService;

    /** @var PostViewingService|Phake_IMock */
    private $postViewingService;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->twig = Phake::mock(Environment::class);
        $this->authenticationService = Phake::mock(AdminAuthenticator::class);
        $this->request = Phake::mock(ServerRequestInterface::class);
        $this->postWritingService = Phake::mock(PostWritingService::class);

        $this->authorService = Phake::mock(AuthorService::class);
        Phake::when($this->authorService)->getAuthors()->thenReturn([]);

        $this->postViewingService = Phake::mock(PostViewingService::class);

        $this->controller = new PostWritingController(
            $this->twig,
            $this->authenticationService,
            $this->request,
            $this->postWritingService,
            $this->authorService,
            $this->postViewingService
        );
    }

    public function testNewPost(): void
    {
        $this->mockAdminAuthentication();

        $this->controller->newPost();

        Phake::verify($this->twig)->display('posts/new.twig', [
            'authors' => [],
            'commentTypes' => CommentType::VALID_COMMENT_TYPES
        ]);
    }

    public function testNewPost_ForbiddenAccess(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->newPost();
    }

    public function testCreatePost_ForbiddenAccess(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        $this->controller->createPost();
    }

    public function testCreatePost_InvalidParameters(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        $this->mockAdminAuthentication();

        $this->controller->createPost();
    }

    public function testCreatePost_CreationError(): void
    {
        $this->mockAdminAuthentication();
        Phake::when($this->postWritingService)->createPost(self::BODY['post'])
            ->thenThrow(new PostWritingException(self::CREATION_ERROR));
        Phake::when($this->request)->getParsedBody()->thenReturn(self::BODY);

        $this->controller->createPost();

        Phake::verify($this->twig)->display('posts/new.twig', [
            'error' => self::CREATION_ERROR,
            'authors' => [],
            'post' => [],
            'commentTypes' => CommentType::VALID_COMMENT_TYPES
        ]);
    }

    public function testCreatePost(): void
    {
        $this->mockAdminAuthentication();
        Phake::when($this->request)->getParsedBody()->thenReturn(self::BODY);
        $post = new Post();
        Phake::when($this->postWritingService)->createPost(self::BODY['post'])->thenReturn($post);

        $this->controller->createPost();

        Phake::verify($this->twig)->display('posts/edit.twig', [
            'success' => self::POST_CREATION_SUCCESS,
            'authors' => [],
            'post' => $post,
            'commentTypes' => CommentType::VALID_COMMENT_TYPES
        ]);
    }

    public function testEditPost(): void
    {
        $this->mockAdminAuthentication();
        $post = $this->getPostFromService();

        $this->controller->editPost(['id' => (string)self::ID]);

        Phake::verify($this->twig)->display('posts/edit.twig', [
            'authors' => [],
            'post' => $post,
            'commentTypes' => CommentType::VALID_COMMENT_TYPES
        ]);
    }

    public function testEditPost_PostNotFound(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::NOT_FOUND);

        $this->mockAdminAuthentication();
        Phake::when($this->postViewingService)->getPost(self::ID)->thenThrow(new PostNotFoundException());

        $this->controller->editPost(['id' => (string)self::ID]);
    }

    public function testUpdatePost(): void
    {
        $this->mockAdminAuthentication();
        $post = $this->getPostFromService();
        Phake::when($this->request)->getParsedBody()->thenReturn(self::BODY);

        $this->controller->updatePost(['id' => (string)self::ID]);

        Phake::verify($this->postWritingService)->updatePost($post, self::BODY['post']);
        Phake::verify($this->twig)->display('posts/edit.twig', [
            'authors' => [],
            'post' => $post,
            'success' => self::POST_UPDATING_SUCCESS,
            'commentTypes' => CommentType::VALID_COMMENT_TYPES
        ]);
    }

    public function testUpdatePost_UpdateFailed(): void
    {
        $this->mockAdminAuthentication();

        $post = $this->getPostFromService();
        Phake::when($this->request)->getParsedBody()->thenReturn(self::BODY);
        Phake::when($this->postWritingService)->updatePost($post, self::BODY['post'])
            ->thenThrow(new PostWritingException('error updating post'));

        $this->controller->updatePost(['id' => (string)self::ID]);

        Phake::verify($this->twig)->display('posts/edit.twig', [
            'authors' => [],
            'post' => $post,
            'error' => 'error updating post',
            'commentTypes' => CommentType::VALID_COMMENT_TYPES
        ]);
    }

    public function testPreviewPost(): void
    {
        $this->mockAdminAuthentication();

        $post = $this->getPostFromService();

        $this->controller->previewPost(['id' => (string) self::ID]);

        Phake::verify($this->twig)->display('posts/preview.twig', ['post' => $post]);
    }

    private function mockAdminAuthentication(): void
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
    }

    private function getPostFromService(): Post
    {
        $post = new Post();
        Phake::when($this->postViewingService)->getPost(self::ID)->thenReturn($post);

        return $post;
    }
}
