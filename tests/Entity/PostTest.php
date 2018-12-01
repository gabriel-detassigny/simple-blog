<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Entity;

use DateTime;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\Post;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    /** @var Post */
    private $post;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->post = new Post();
    }

    public function testGetText(): void
    {
        $this->post->setText('Text');

        $this->assertSame('Text', $this->post->getText());
    }

    public function testGetTitle(): void
    {
        $this->post->setTitle('Title');

        $this->assertSame('Title', $this->post->getTitle());
    }

    public function testGetSubtitle(): void
    {
        $this->post->setSubtitle('Subtitle');

        $this->assertSame('Subtitle', $this->post->getSubtitle());
    }

    public function testGetCreatedAt(): void
    {
        $date = new DateTime();
        $this->post->setCreatedAt($date);

        $this->assertSame($date, $this->post->getCreatedAt());
    }

    public function testGetAuthor(): void
    {
        $author = new Author();
        $this->post->setAuthor($author);

        $this->assertSame($author, $this->post->getAuthor());
    }
}
