<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Repository;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\ValueObject\Page;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class PostRepositoryTest extends TestCase
{
    /** @var ClassMetadata|Phake_IMock */
    private $classMetadata;

    /** @var EntityManager|Phake_IMock */
    private $entityManager;

    /** @var PostRepository */
    private $repository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->classMetadata = Phake::mock(ClassMetadata::class);
        $this->entityManager = Phake::mock(EntityManager::class);
        $this->repository = new PostRepository($this->entityManager, $this->classMetadata);
    }

    public function testSearchPageOfLatestPosts()
    {
        Phake::when($this->entityManager)->createQuery('SELECT p FROM ' . Post::class . ' p ORDER BY p.createdAt DESC')
            ->thenCallParent();
        Phake::when($this->entityManager)->getConfiguration()->thenReturn(new Configuration);

        $response = $this->repository->searchPageOfLatestPosts(new Page(1), 10);

        $this->assertInstanceOf(Paginator::class, $response);
    }
}
