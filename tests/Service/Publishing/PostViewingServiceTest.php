<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service\Publishing;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\Service\Exception\PostNotFoundException;
use GabrielDeTassigny\Blog\Service\Publishing\PostViewingService;
use GabrielDeTassigny\Blog\ValueObject\PostState;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class PostViewingServiceTest extends TestCase
{
    private const LIMIT = 10;
    private const POST_ID = 2;

    /** @var PostRepository|Phake_IMock */
    private $repository;

    /** @var PostViewingService|Phake_IMock */
    private $service;

    public function setUp(): void
    {
        $this->repository = Phake::mock(PostRepository::class);
        $this->service = new PostViewingService($this->repository);
    }

    public function testFindLatestPublishedPosts(): void
    {
        $paginator = Phake::mock(Paginator::class);
        Phake::when($this->repository)->searchPageOfLatestPosts(Phake::anyParameters())->thenReturn($paginator);

        $this->assertSame($paginator, $this->service->findLatestPublishedPosts(self::LIMIT));

        Phake::verify($this->repository)->searchPageOfLatestPosts(
            Phake::capture($page),
            self::LIMIT,
            Phake::capture($postState)
        );

        $this->assertSame(1, $page->getValue());
        $this->assertSame(PostState::PUBLISHED, $postState->getValue());
    }

    public function testFindLatestDraftPosts(): void
    {
        $paginator = Phake::mock(Paginator::class);
        Phake::when($this->repository)->searchPageOfLatestPosts(Phake::anyParameters())->thenReturn($paginator);

        $this->assertSame($paginator, $this->service->findLatestDraftPosts(self::LIMIT));

        Phake::verify($this->repository)->searchPageOfLatestPosts(
            Phake::capture($page),
            self::LIMIT,
            Phake::capture($postState)
        );

        $this->assertSame(1, $page->getValue());
        $this->assertSame(PostState::DRAFT, $postState->getValue());
    }

    public function testGetPost(): void
    {
        $post = Phake::mock(Post::class);
        Phake::when($this->repository)->find(self::POST_ID)->thenReturn($post);

        $this->assertSame($post, $this->service->getPost(self::POST_ID));
    }

    public function testGetPost_ThrowsErrorIfNotFound(): void
    {
        $this->expectException(PostNotFoundException::class);

        Phake::when($this->repository)->find(self::POST_ID)->thenReturn(null);

        $this->service->getPost(self::POST_ID);
    }
}
