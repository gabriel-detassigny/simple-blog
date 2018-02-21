<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Entity;

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

    public function testGetText()
    {
        $this->post->setText('Text');

        $this->assertSame('Text', $this->post->getText());
    }

    public function testGetTitle()
    {
        $this->post->setTitle('Title');

        $this->assertSame('Title', $this->post->getTitle());
    }

    public function testGetSubtitle()
    {
        $this->post->setSubtitle('Subtitle');

        $this->assertSame('Subtitle', $this->post->getSubtitle());
    }
}
