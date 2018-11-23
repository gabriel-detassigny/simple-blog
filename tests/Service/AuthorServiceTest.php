<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Repository\AuthorRepository;
use GabrielDeTassigny\Blog\Service\AuthorNotFoundException;
use GabrielDeTassigny\Blog\Service\AuthorService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class AuthorServiceTest extends TestCase
{
    /** @var AuthorRepository|Phake_IMock */
    private $authorRepository;

    /** @var AuthorService */
    private $service;

    const ID = 1;

    public function setUp()
    {
        parent::setUp();
        $this->authorRepository = Phake::mock(AuthorRepository::class);
        $this->service = new AuthorService($this->authorRepository);
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
        $this->expectException(AuthorNotFoundException::class);
        Phake::when($this->authorRepository)->find(self::ID)->thenReturn(null);

        $this->service->getAuthorById(self::ID);
    }

    private function getAuthorEntity(string $name): Author
    {
        $author = new Author();
        $author->setName($name);

        return $author;
    }
}
