<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\Comment;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Service\CommentException;
use GabrielDeTassigny\Blog\Service\CommentService;
use GabrielDeTassigny\Blog\Service\PostNotFoundException;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class CommentServiceTest extends TestCase
{
    private const PARAMS = [
        'text' => 'comment text',
        'name' => 'test user'
    ];

    private const POST_ID = 1;

    /** @var EntityManager|Phake_IMock */
    private $entityManager;

    /** @var PostViewingService|Phake_IMock */
    private $postViewingService;

    /** @var CommentService */
    private $commentService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->entityManager = Phake::mock(EntityManager::class);
        $this->postViewingService = Phake::mock(PostViewingService::class);
        $this->commentService = new CommentService($this->entityManager, $this->postViewingService);
    }

    public function testCreateComment(): void
    {
        $post = new Post();
        Phake::when($this->postViewingService)->getPost(self::POST_ID)->thenReturn($post);

        $comment = $this->commentService->createComment(self::PARAMS, self::POST_ID);

        Phake::verify($this->entityManager)->persist($comment);
        Phake::verify($this->entityManager)->flush();
        $this->assertSame($post, $comment->getPost());
        $this->assertSame(self::PARAMS['text'], $comment->getText());
        $this->assertSame(self::PARAMS['name'], $comment->getName());
    }

    public function testCreateComment_PostNotFound(): void
    {
        $this->expectException(CommentException::class);
        $this->expectExceptionCode(CommentException::FIELD_ERROR);

        Phake::when($this->postViewingService)->getPost(self::POST_ID)->thenThrow(new PostNotFoundException());

        $this->commentService->createComment(self::PARAMS, self::POST_ID);
    }

    public function testCreateComment_DoctrineError(): void
    {
        $this->expectException(CommentException::class);
        $this->expectExceptionCode(CommentException::DB_ERROR);

        Phake::when($this->entityManager)->persist(Phake::anyParameters())->thenThrow(new ORMException());
        Phake::when($this->postViewingService)->getPost(self::POST_ID)->thenReturn(new Post());

        $this->commentService->createComment(self::PARAMS, self::POST_ID);
    }

    public function testCreateComment_EmptyText(): void
    {
        $this->expectException(CommentException::class);
        $this->expectExceptionCode(CommentException::FIELD_ERROR);

        Phake::when($this->postViewingService)->getPost(self::POST_ID)->thenReturn(new Post());
        $params = self::PARAMS;
        $params['text'] = '';

        $this->commentService->createComment($params, self::POST_ID);
    }

    public function testCreateComment_EmptyName(): void
    {
        $this->expectException(CommentException::class);
        $this->expectExceptionCode(CommentException::FIELD_ERROR);

        Phake::when($this->postViewingService)->getPost(self::POST_ID)->thenReturn(new Post());
        $params = self::PARAMS;
        $params['name'] = '';

        $this->commentService->createComment($params, self::POST_ID);
    }

    public function testGetPostComments(): void
    {
        $post = Phake::mock(Post::class);
        $comments = new ArrayCollection();
        $comments->add(new Comment());
        Phake::when($post)->getComments()->thenReturn($comments);
        Phake::when($this->postViewingService)->getPost(1)->thenReturn($post);

        $result = $this->commentService->getPostComments(1);

        $this->assertSame($comments, $result);
    }

    public function testGetPostComments_PostNotFound(): void
    {
        $this->expectException(CommentException::class);

        Phake::when($this->postViewingService)->getPost(1)->thenThrow(new PostNotFoundException());

        $this->commentService->getPostComments(1);
    }
}
