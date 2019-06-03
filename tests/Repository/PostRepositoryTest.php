<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Repository;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\ValueObject\Page;
use GabrielDeTassigny\Blog\ValueObject\PostState;

class PostRepositoryTest extends RepositoryTestCase
{
    private const NAME = 'Commenter name';
    private const TEXT = 'some comment text';
    private const TITLE = 'post title';
    private const SUBTITLE = 'some subtitle';

    /** @var EntityManager */
    private $entityManager;

    /** @var PostRepository */
    private $repository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->entityManager = $this->createTestEntityManager();
        $this->repository = $this->entityManager->getRepository(Post::class);
    }

    public function testCreateAndFindPost(): void
    {
        $dateTime = new DateTime();
        $author = $this->buildAuthor();
        $post = $this->buildPost($dateTime, $author);

        $this->entityManager->persist($author);
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        /** @var Post $entity */
        $entity = $this->repository->find($post->getId());

        $this->assertNotNull($entity->getId());
        $this->assertSame(self::TITLE, $entity->getTitle());

        $this->assertSame(self::SUBTITLE, $entity->getSubtitle());
        $this->assertSame(self::TEXT, $entity->getText());
        $this->assertSame($dateTime->getTimestamp(), $entity->getCreatedAt()->getTimestamp());
        $this->assertInstanceOf(Author::class, $entity->getAuthor());
        $this->assertInstanceOf(Collection::class, $entity->getComments());
    }

    public function testSearchPageOfLatestPublishedPosts(): void
    {
        $dateTime = new DateTime();
        $author = $this->buildAuthor();
        $this->entityManager->persist($author);
        for ($i = 0; $i < 10; $i++) {
            $this->entityManager->persist($this->buildPost($dateTime, $author));
        }
        $this->entityManager->flush();

        $postState = new PostState(PostState::PUBLISHED);

        $posts = $this->repository->searchPageOfLatestPosts(new Page(1), 10, $postState);

        $this->assertSame(10, $posts->count());
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
        $post->setState(new PostState(PostState::PUBLISHED));

        return $post;
    }

    private function buildAuthor(): Author
    {
        $author = new Author();

        $author->setName(self::NAME);

        return $author;
    }
}
