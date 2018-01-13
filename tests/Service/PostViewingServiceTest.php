<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\Service\PostViewingService;
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

    public function testFindPageOfLatestPosts()
    {
        $pageResult = Phake::mock(Paginator::class);
        Phake::when($this->repository)->searchPageOfLatestPosts(Phake::anyParameters())->thenReturn($pageResult);

        $this->assertSame($pageResult, $this->service->findPageOfLatestPosts());
    }
}
