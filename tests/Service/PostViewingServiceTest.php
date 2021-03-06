<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\Service\Exception\PostNotFoundException;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use GabrielDeTassigny\Blog\ValueObject\Page;
use GabrielDeTassigny\Blog\ValueObject\PostState;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class PostViewingServiceTest extends TestCase
{
    const POST_ID = 1;

    /** @var PostViewingService */
    private $service;

    /** @var PostRepository|Phake_IMock */
    private $repository;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->repository = Phake::mock(PostRepository::class);
        $this->service = new PostViewingService($this->repository);
    }

    public function testFindPageOfLatestPosts(): void
    {
        $pageResult = Phake::mock(Paginator::class);
        Phake::when($this->repository)->searchPageOfLatestPosts(Phake::anyParameters())->thenReturn($pageResult);

        $this->assertSame($pageResult, $this->service->findPageOfLatestPosts(new Page(self::POST_ID)));
    }

    public function testGetPreviousPage(): void
    {
        $currentPage = new Page(2);

        $previousPage = $this->service->getPreviousPage($currentPage);

        $this->assertSame(self::POST_ID, $previousPage->getValue());
    }

    public function testGetPreviousPage_CurrentPageIsFirst(): void
    {
        $currentPage = new Page(self::POST_ID);

        $previousPage = $this->service->getPreviousPage($currentPage);

        $this->assertNull($previousPage);
    }

    public function testGetNextPage(): void
    {
        $currentPage = new Page(self::POST_ID);

        $nextPage = $this->service->getNextPage($currentPage, 20);

        $this->assertSame(2, $nextPage->getValue());
    }

    public function testGetNextPage_CurrentPageIsLastPage(): void
    {
        $currentPage = new Page(self::POST_ID);

        $nextPage = $this->service->getNextPage($currentPage, 9);

        $this->assertNull($nextPage);
    }

    public function testGetPost_NotFound(): void
    {
        $this->expectException(PostNotFoundException::class);

        $this->service->getPost(self::POST_ID);
    }

    public function testGetPost(): void
    {
        Phake::when($this->repository)->findOneBy(['id' => self::POST_ID, 'state' => PostState::PUBLISHED])
            ->thenReturn(Phake::mock(Post::class));

        $post = $this->service->getPost(self::POST_ID);

        $this->assertInstanceOf(Post::class, $post);
    }
}
