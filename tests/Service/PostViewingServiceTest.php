<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use GabrielDeTassigny\Blog\ValueObject\Page;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class PostViewingServiceTest extends TestCase
{

    /** @var PostViewingService */
    private $service;

    /** @var PostRepository|Phake_IMock */
    private $repository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->repository = Phake::mock(PostRepository::class);
        $this->service = new PostViewingService($this->repository);
    }

    public function testFindPageOfLatestPosts(): void
    {
        $pageResult = Phake::mock(Paginator::class);
        Phake::when($this->repository)->searchPageOfLatestPosts(Phake::anyParameters())->thenReturn($pageResult);

        $this->assertSame($pageResult, $this->service->findPageOfLatestPosts(new Page(1)));
    }

    public function testGetPreviousPage(): void
    {
        $currentPage = new Page(2);

        $previousPage = $this->service->getPreviousPage($currentPage);

        $this->assertSame(1, $previousPage->getValue());
    }

    public function testGetPreviousPage_CurrentPageIsFirst(): void
    {
        $currentPage = new Page(1);

        $previousPage = $this->service->getPreviousPage($currentPage);

        $this->assertNull($previousPage);
    }

    public function testGetNextPage(): void
    {
        $currentPage = new Page(1);

        $nextPage = $this->service->getNextPage($currentPage, 20);

        $this->assertSame(2, $nextPage->getValue());
    }

    public function testGetNextPage_CurrentPageIsLastPage(): void
    {
        $currentPage = new Page(1);

        $nextPage = $this->service->getNextPage($currentPage, 9);

        $this->assertNull($nextPage);
    }
}
