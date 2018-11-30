<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Service\AuthorException;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\PostWritingException;
use GabrielDeTassigny\Blog\Service\PostWritingService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class PostWritingServiceTest extends TestCase
{
    /** @var PostWritingService */
    private $service;

    /** @var EntityManager|Phake_IMock */
    private $entityManager;

    /** @var AuthorService|Phake_IMock */
    private $authorService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->entityManager = Phake::mock(EntityManager::class);
        $this->authorService = Phake::mock(AuthorService::class);
        $this->service = new PostWritingService($this->entityManager, $this->authorService);
    }

    public function testCreatePost(): void
    {
        $request = ['text' => 'example of text', 'title' => 'Title', 'subtitle' => 'Subtitle', 'author' => 1];
        Phake::when($this->authorService)->getAuthorById(1)->thenReturn(Phake::mock(Author::class));

        $post = $this->service->createPost($request);

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
        $request = ['text' => 'example of text', 'title' => 'Title', 'subtitle' => 'Subtitle', 'author' => 1];
        Phake::when($this->authorService)->getAuthorById(1)->thenReturn(Phake::mock(Author::class));

        $this->service->createPost($request);
    }

    public function testCreatePost_AuthorNotFound(): void
    {
        $this->expectException(PostWritingException::class);
        $this->expectExceptionCode(PostWritingException::AUTHOR_ERROR);

        $request = ['text' => 'example of text', 'title' => 'Title', 'subtitle' => 'Subtitle', 'author' => 1];
        Phake::when($this->authorService)->getAuthorById(1)->thenThrow(new AuthorException());

        $this->service->createPost($request);
    }

    public function testUpdatePost(): void
    {
        $request = ['text' => 'example of text', 'title' => 'Title', 'subtitle' => 'Subtitle', 'author' => 1];
        $author = Phake::mock(Author::class);
        Phake::when($this->authorService)->getAuthorById(1)->thenReturn($author);
        $post = Phake::partialMock(Post::class);

        $this->service->updatePost($post, $request);

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
        $request = ['text' => 'example of text', 'title' => 'Title', 'subtitle' => 'Subtitle', 'author' => 1];
        Phake::when($this->authorService)->getAuthorById(1)->thenReturn(Phake::mock(Author::class));

        $this->service->updatePost($post, $request);
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
}
