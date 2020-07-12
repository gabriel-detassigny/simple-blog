<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Entity;

use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\ExternalLink;
use Phake;
use PHPUnit\Framework\TestCase;

class AuthorTest extends TestCase
{
    /** @var Author */
    private $author;

    protected function setUp(): void
    {
        $this->author = new Author();
    }

    public function testGetExternalLinksReturnsEmptyCollection(): void
    {
        $this->assertEmpty($this->author->getExternalLinks());
    }

    public function testAddExternalLink(): void
    {
        $externalLink = Phake::mock(ExternalLink::class);

        $this->author->addExternalLink($externalLink);

        $this->assertCount(1, $this->author->getExternalLinks());
    }

    public function testRemoveExternalLink(): void
    {
        $externalLink1 = Phake::mock(ExternalLink::class);
        $externalLink2 = Phake::mock(ExternalLink::class);

        $this->author->addExternalLink($externalLink1);
        $this->author->addExternalLink($externalLink2);

        $this->author->removeExternalLink($externalLink1);

        $this->assertCount(1, $this->author->getExternalLinks());

    }
}
