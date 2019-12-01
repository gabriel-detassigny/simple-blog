<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller\Admin;

use Doctrine\Common\Collections\Collection;
use GabrielDeTassigny\Blog\Controller\Admin\CommentAdminController;
use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\Exception\CommentException;
use GabrielDeTassigny\Blog\Service\CommentService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class CommentAdminControllerTest extends TestCase
{
    private const POST_ID = 1;
    private const AUTHOR_COMMENT = 'Author comment';

    /** @var AdminAuthenticator|Phake_IMock */
    private $authenticationService;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /** @var CommentService|Phake_IMock */
    private $commentService;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /** @var CommentAdminController */
    private $commentAdminController;

    public function setUp(): void
    {
        $this->authenticationService = Phake::mock(AdminAuthenticator::class);
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->commentService = Phake::mock(CommentService::class);
        $this->request = Phake::mock(ServerRequestInterface::class);

        $this->commentAdminController = new CommentAdminController(
            $this->authenticationService,
            $this->twig,
            $this->commentService,
            $this->request
        );

        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
    }

    public function testIndex(): void
    {
        $comments = Phake::mock(Collection::class);
        Phake::when($this->commentService)->getPostComments(self::POST_ID)->thenReturn($comments);

        $this->commentAdminController->index(['id' => self::POST_ID]);

        Phake::verify($this->twig)->display('comments/list.twig', ['comments' => $comments, 'postId' => self::POST_ID]);
    }

    public function testIndex_ServiceError(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::INTERNAL_SERVER_ERROR);

        Phake::when($this->commentService)->getPostComments(self::POST_ID)->thenThrow(new CommentException());

        $this->commentAdminController->index(['id' => self::POST_ID]);
    }

    public function testNewComment(): void
    {
        $this->commentAdminController->newComment(['id' => self::POST_ID]);

        Phake::verify($this->twig)->display('comments/new.twig', ['postId' => self::POST_ID]);
    }

    public function testCreateComment(): void
    {
        Phake::when($this->request)->getParsedBody()->thenReturn(['comment' => ['text' => self::AUTHOR_COMMENT]]);

        $this->commentAdminController->createComment(['id' => self::POST_ID]);

        Phake::verify($this->commentService)->createAdminComment(self::AUTHOR_COMMENT, self::POST_ID);
        Phake::verify($this->twig)
            ->display('comments/new.twig', ['success' => 'Comment added!', 'postId' => self::POST_ID]);
    }

    public function testCreateComment_InvalidParams(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::BAD_REQUEST);

        Phake::when($this->request)->getParsedBody()->thenReturn([]);

        $this->commentAdminController->createComment(['id' => self::POST_ID]);
    }

    public function testCreateComment_ServiceError(): void
    {
        Phake::when($this->commentService)->createAdminComment(self::AUTHOR_COMMENT, self::POST_ID)
            ->thenThrow(new CommentException('Error in creation'));
        Phake::when($this->request)->getParsedBody()->thenReturn(['comment' => ['text' => self::AUTHOR_COMMENT]]);

        $this->commentAdminController->createComment(['id' => self::POST_ID]);

        Phake::verify($this->twig)
            ->display('comments/new.twig', ['error' => 'Error in creation', 'postId' => self::POST_ID]);
    }

    public function testDeleteComment_ServiceError(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::INTERNAL_SERVER_ERROR);

        Phake::when($this->commentService)->deleteComment(self::POST_ID)
            ->thenThrow(new CommentException('Error deleting'));

        $this->commentAdminController->deleteComment(['id' => self::POST_ID]);
    }

    public function testDeleteComment(): void
    {
        $this->commentAdminController->deleteComment(['id' => self::POST_ID]);

        Phake::verify($this->commentService)->deleteComment(self::POST_ID);
    }
}
