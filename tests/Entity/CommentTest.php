<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Entity;

use DateTime;
use GabrielDeTassigny\Blog\Entity\Comment;
use GabrielDeTassigny\Blog\Entity\Post;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    private const NAME = 'Test user';
    private const TEXT = 'some comment';

    /** @var Comment */
    private $comment;

    public function setUp(): void
    {
        $this->comment = new Comment();
    }

    public function testGetPost()
    {
        $post = new Post();

        $this->comment->setPost($post);

        $this->assertSame($post, $this->comment->getPost());
    }

    public function testIsAdmin()
    {
        $this->comment->setAsAdmin();

        $this->assertTrue($this->comment->IsAdmin());
    }

    public function testGetCreatedAt()
    {
        $date = new DateTime();

        $this->comment->setCreatedAt($date);

        $this->assertSame($date, $this->comment->getCreatedAt());
    }

    public function testGetName()
    {
        $this->comment->setName(self::NAME);

        $this->assertSame(self::NAME, $this->comment->getName());
    }

    public function testGetText()
    {
        $this->comment->setText(self::TEXT);

        $this->assertSame(self::TEXT, $this->comment->getText());
    }
}
