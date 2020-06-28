<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service\Publishing;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Service\Exception\AuthorException;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\Exception\PostWritingException;
use GabrielDeTassigny\Blog\Service\Publishing\PostWritingService;
use GabrielDeTassigny\Blog\ValueObject\CommentType;
use GabrielDeTassigny\Blog\ValueObject\PostState;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class PostWritingServiceTest extends TestCase
{
    private const REQUEST = [
        'text' => 'example of text',
        'title' => 'Title',
        'subtitle' => 'Subtitle',
        'author' => 1,
        'state' => PostState::DRAFT,
        'comment-type' => CommentType::INTERNAL
    ];

    /** @var PostWritingService */
    private $service;

    /** @var EntityManager|Phake_IMock */
    private $entityManager;

    /** @var AuthorService|Phake_IMock */
    private $authorService;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->entityManager = Phake::mock(EntityManager::class);
        $this->authorService = Phake::mock(AuthorService::class);
        $this->service = new PostWritingService($this->entityManager, $this->authorService);
    }

    public function testCreatePost(): void
    {
        Phake::when($this->authorService)->getAuthorById(1)->thenReturn(Phake::mock(Author::class));

        $post = $this->service->createPost(self::REQUEST);

        Phake::inOrder(
            Phake::verify($this->entityManager)->persist($post),
            Phake::verify($this->entityManager)->flush()
        );
    }

    public function testCreatePost_DoctrineException(): void
    {
        $this->expectException(PostWritingException::class);
        $this->expectExceptionCode(PostWritingException::DB_ERROR);

        Phake::when($this->entityManager)->persist(Phake::anyParameters())->thenThrow(new ORMException());
        Phake::when($this->authorService)->getAuthorById(1)->thenReturn(Phake::mock(Author::class));

        $this->service->createPost(self::REQUEST);
    }

    public function testCreatePost_AuthorNotFound(): void
    {
        $this->expectException(PostWritingException::class);
        $this->expectExceptionCode(PostWritingException::AUTHOR_ERROR);

        Phake::when($this->authorService)->getAuthorById(1)->thenThrow(new AuthorException());

        $this->service->createPost(self::REQUEST);
    }

    public function testUpdatePost(): void
    {
        $author = Phake::mock(Author::class);
        Phake::when($this->authorService)->getAuthorById(1)->thenReturn($author);
        $post = Phake::partialMock(Post::class);

        $this->service->updatePost($post, self::REQUEST);

        Phake::verify($post)->setText('example of text');
        Phake::verify($post)->setTitle('Title');
        Phake::verify($post)->setSubtitle('Subtitle');
        Phake::verify($post)->setAuthor($author);
        Phake::verify($this->entityManager)->persist($post);
        Phake::verify($this->entityManager)->flush();
    }

    public function testUpdatePost_DoctrineException(): void
    {
        $this->expectException(PostWritingException::class);
        $this->expectExceptionCode(PostWritingException::DB_ERROR);

        $post = new Post();
        Phake::when($this->entityManager)->persist($post)->thenThrow(new ORMException());
        Phake::when($this->authorService)->getAuthorById(1)->thenReturn(Phake::mock(Author::class));

        $this->service->updatePost($post, self::REQUEST);
    }

    public function testUpdatePost_EmptyTitle(): void
    {
        $this->expectException(PostWritingException::class);
        $this->expectExceptionCode(PostWritingException::TITLE_ERROR);

        $request = ['text' => 'example of text', 'title' => '', 'subtitle' => 'Subtitle', 'author' => 1];
        $post = new Post();

        $this->service->updatePost($post, $request);
    }

    public function testUpdatePost_EmptyText(): void
    {
        $this->expectException(PostWritingException::class);
        $this->expectExceptionCode(PostWritingException::TEXT_ERROR);

        $request = ['text' => '', 'title' => 'Title', 'subtitle' => 'Subtitle', 'author' => 1];
        $post = new Post();

        $this->service->updatePost($post, $request);
    }

    public function testUpdatePost_InvalidState(): void
    {
        $this->expectException(PostWritingException::class);
        $this->expectExceptionCode(PostWritingException::STATE_ERROR);

        $request = ['text' => 'test', 'title' => 'Title', 'subtitle' => 'Subtitle', 'author' => 1, 'state' => ''];
        $post = new Post();

        $this->service->updatePost($post, $request);
    }
}
