<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Repository;

use Doctrine\ORM\EntityManager;
use GabrielDeTassigny\Blog\Entity\BlogInfo;
use GabrielDeTassigny\Blog\Repository\BlogInfoRepository;

class BlogInfoRepositoryTest extends RepositoryTestCase
{
    private const KEY = 'some-key';
    private const VALUE = 'some-value';

    /** @var EntityManager */
    private $entityManager;

    /** @var BlogInfoRepository */
    private $repository;

    public function setUp(): void
    {
        $this->entityManager = $this->createTestEntityManager();
        $this->repository = $this->entityManager->getRepository(BlogInfo::class);
    }

    public function testCreateAndFindBlogInfo(): void
    {
        $blogInfo = $this->buildBlogInfo();
        $this->entityManager->persist($blogInfo);
        $this->entityManager->flush();

        $entity = $this->repository->find($blogInfo->getId());

        $this->assertNotNull($entity->getId());
        $this->assertSame(self::KEY, $entity->getKey());
        $this->assertSame(self::VALUE, $entity->getValue());
    }

    private function buildBlogInfo(): BlogInfo
    {
        $blogInfo = new BlogInfo();

        $blogInfo->setKey(self::KEY);
        $blogInfo->setValue(self::VALUE);

        return $blogInfo;
    }
}
