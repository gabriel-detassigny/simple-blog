<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Repository;

use DateTime;
use Doctrine\ORM\EntityManager;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\Comment;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Repository\CommentRepository;

class CommentRepositoryTest extends RepositoryTestCase
{
    private const NAME = 'Commenter name';
    private const TEXT = 'some comment text';
    private const TITLE = 'post title';
    private const SUBTITLE = 'some subtitle';

    /** @var EntityManager */
    private $entityManager;

    /** @var CommentRepository */
    private $repository;

    public function setUp()
    {
        $this->entityManager = $this->createTestEntityManager();
        $this->repository = $this->entityManager->getRepository(Comment::class);
    }

    public function testCreateAndFindComment(): void
    {
        $dateTime = new DateTime();
        $author = $this->buildAuthor();
        $post = $this->buildPost($dateTime, $author);
        $comment = $this->buildComment($dateTime, $post);

        $this->entityManager->persist($author);
        $this->entityManager->persist($post);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        /** @var Comment $entity */
        $entity = $this->repository->find($comment->getId());

        $this->assertNotNull($entity->getId());
        $this->assertSame(self::NAME, $entity->getName());
        $this->assertSame(self::TEXT, $entity->getText());
        $this->assertSame($dateTime->getTimestamp(), $entity->getCreatedAt()->getTimestamp());
        $this->assertInstanceOf(Post::class, $entity->getPost());
    }

    private function buildComment(DateTime $dateTime, Post $post): Comment
    {

        $comment = new Comment();

        $comment->setName(self::NAME);
        $comment->setText(self::TEXT);
        $comment->setAsAdmin();
        $comment->setCreatedAt($dateTime);
        $comment->setPost($post);

        return $comment;
    }

    private function buildPost(DateTime $dateTime, Author $author): Post
    {
        $post = new Post();

        $post->setCreatedAt($dateTime);
        $post->setText(self::TEXT);
        $post->setTitle(self::TITLE);
        $post->setSubtitle(self::SUBTITLE);
        $post->setAuthor($author);
        $post->setUpdatedAt($dateTime);

        return $post;
    }

    private function buildAuthor(): Author
    {
        $author = new Author();

        $author->setName(self::NAME);

        return $author;
    }
}
