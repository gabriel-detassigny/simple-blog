<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\ExternalLink;
use GabrielDeTassigny\Blog\Repository\AuthorRepository;
use GabrielDeTassigny\Blog\Service\Exception\AuthorException;
use GabrielDeTassigny\Blog\Service\AuthorService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class AuthorServiceTest extends TestCase
{
    private const ID = 1;

    /** @var AuthorRepository|Phake_IMock */
    private $authorRepository;

    /** @var EntityManager|Phake_IMock */
    private $entityManager;

    /** @var AuthorService */
    private $service;

    public function setUp(): void
    {
        $this->authorRepository = Phake::mock(AuthorRepository::class);
        $this->entityManager = Phake::mock(EntityManager::class);
        $this->service = new AuthorService($this->authorRepository, $this->entityManager);
    }

    public function testGetAuthors(): void
    {
        Phake::when($this->authorRepository)->findAll()
            ->thenReturn([$this->getAuthorEntity('Stephen King'), $this->getAuthorEntity('Robin Hobb')]);

        $authors = $this->service->getAuthors();

        $this->assertCount(2, $authors);
        $this->assertSame('Stephen King', $authors[0]->getName());
        $this->assertSame('Robin Hobb', $authors[1]->getName());
    }

    public function testGetAuthorById(): void
    {
        Phake::when($this->authorRepository)->find(self::ID)->thenReturn($this->getAuthorEntity('Stephen King'));

        $author = $this->service->getAuthorById(self::ID);

        $this->assertSame('Stephen King', $author->getName());
    }

    public function testGetAuthorById_NotFound(): void
    {
        $this->expectException(AuthorException::class);
        $this->expectExceptionCode(AuthorException::FIND_ERROR);
        Phake::when($this->authorRepository)->find(self::ID)->thenReturn(null);

        $this->service->getAuthorById(self::ID);
    }

    public function testCreateAuthor(): void
    {
        $author = $this->service->createAuthor('Stephen King');

        Phake::inOrder(
            Phake::verify($this->entityManager)->persist($author),
            Phake::verify($this->entityManager)->flush()
        );
    }

    public function testCreateAuthor_DatabaseError(): void
    {
        $this->expectException(AuthorException::class);
        $this->expectExceptionCode(AuthorException::CREATE_ERROR);

        Phake::when($this->entityManager)->persist(Phake::anyParameters())->thenThrow(new ORMException());

        $this->service->createAuthor('Stephen King');
    }

    public function testAddExternalLink(): void
    {
        $author = $this->getAuthorEntity('Stephen King');
        Phake::when($this->authorRepository)->find(self::ID)->thenReturn($author);

        $this->service->addExternalLink(self::ID, new ExternalLink());

        $this->assertCount(1, $author->getExternalLinks());
    }

    public function testAddExternalLink_DatabaseError(): void
    {
        $this->expectException(AuthorException::class);
        $this->expectExceptionCode(AuthorException::LINK_ASSOCIATION_ERROR);

        $author = $this->getAuthorEntity('Stephen King');
        Phake::when($this->authorRepository)->find(self::ID)->thenReturn($author);
        Phake::when($this->entityManager)->persist($author)->thenThrow(new ORMException());

        $this->service->addExternalLink(self::ID, new ExternalLink());
    }

    public function testRemoveExternalLink(): void
    {
        $externalLink = new ExternalLink();

        $author = $this->getAuthorEntity('Stephen King');
        $author->addExternalLink($externalLink);
        Phake::when($this->authorRepository)->find(self::ID)->thenReturn($author);

        $this->service->removeExternalLink(self::ID, $externalLink);

        $this->assertEmpty($author->getExternalLinks());
    }

    public function testRemoveExternalLink_DatabaseError(): void
    {
        $this->expectException(AuthorException::class);
        $this->expectExceptionCode(AuthorException::LINK_ASSOCIATION_ERROR);

        $externalLink = new ExternalLink();

        $author = $this->getAuthorEntity('Stephen King');
        $author->addExternalLink($externalLink);
        Phake::when($this->authorRepository)->find(self::ID)->thenReturn($author);

        Phake::when($this->entityManager)->persist($author)->thenThrow(new ORMException());

        $this->service->removeExternalLink(self::ID, new ExternalLink());
    }

    private function getAuthorEntity(string $name): Author
    {
        $author = new Author();
        $author->setName($name);

        return $author;
    }
}
