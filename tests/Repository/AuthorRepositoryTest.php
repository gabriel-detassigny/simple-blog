<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Repository;

use Doctrine\ORM\EntityManager;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Repository\AuthorRepository;

class AuthorRepositoryTest extends RepositoryTestCase
{
    private const NAME = 'John Doe';

    /** @var EntityManager */
    private $entityManager;

    /** @var AuthorRepository */
    private $repository;

    public function setUp(): void
    {
        $this->entityManager = $this->createTestEntityManager();
        $this->repository = $this->entityManager->getRepository(Author::class);
    }

    public function testCreateAndFindAuthor(): void
    {
        $author = $this->buildAuthor();
        $this->entityManager->persist($author);
        $this->entityManager->flush();

        $entity = $this->repository->find($author->getId());

        $this->assertNotNull($entity->getId());
        $this->assertSame(self::NAME, $entity->getName());
    }

    private function buildAuthor(): Author
    {
        $author = new Author();

        $author->setName(self::NAME);

        return $author;
    }
}
