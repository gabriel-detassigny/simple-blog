<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Entity;

use DateTime;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\ValueObject\CommentType;
use GabrielDeTassigny\Blog\ValueObject\PostState;
use Phake;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    /** @var Post */
    private $post;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
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

    public function testGetSlug(): void
    {
        $this->post->setTitle('This is 1 test for @slug');

        $this->assertSame('this-is-1-test-for-slug', $this->post->getSlug());
    }

    public function testGetSlug_UndefinedSlug(): void
    {
        $this->post->setTitle('"+_*');

        $this->assertSame('n-a', $this->post->getSlug());
    }

    public function testGetUrl(): void
    {
        $this->post = Phake::partialMock(Post::class);
        Phake::when($this->post)->getId()->thenReturn(1);
        $this->post->setTitle('This is a title');

        $this->assertSame('/posts/1/this-is-a-title', $this->post->getUrl());
    }

    public function testSetPostAsDraft(): void
    {
        $this->post->setState(new PostState(PostState::DRAFT));

        $this->assertTrue($this->post->isDraft());
        $this->assertFalse($this->post->isPublished());
    }

    public function testSetPostAsPublished(): void
    {
        $this->post->setState(new PostState(PostState::PUBLISHED));

        $this->assertTrue($this->post->isPublished());
        $this->assertFalse($this->post->isDraft());
    }

    public function testSetCommentTypeAsInternal(): void
    {
        $this->post->setCommentType(new CommentType(CommentType::INTERNAL));

        $this->assertTrue($this->post->hasInternalComments());
        $this->assertFalse($this->post->hasLinkedComments());
    }

    public function testSetCommentTypeAsLink(): void
    {
        $this->post->setCommentType(new CommentType(CommentType::LINK));

        $this->assertFalse($this->post->hasInternalComments());
        $this->assertTrue($this->post->hasLinkedComments());
    }
}
