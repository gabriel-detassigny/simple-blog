<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Repository;

use Doctrine\ORM\EntityManager;
use GabrielDeTassigny\Blog\Entity\ExternalLink;
use GabrielDeTassigny\Blog\Repository\ExternalLinkRepository;

class ExternalLinkRepositoryTest extends RepositoryTestCase
{
    private const NAME = 'Github';
    private const URL = 'https://github.com/some-user';

    /** @var EntityManager */
    private $entityManager;

    /** @var ExternalLinkRepository */
    private $repository;

    public function setUp()
    {
        $this->entityManager = $this->createTestEntityManager();
        $this->repository = $this->entityManager->getRepository(ExternalLink::class);
    }

    public function testCreateAndFindExternalLink(): void
    {
        $externalLink = $this->buildExternalLink();
        $this->entityManager->persist($externalLink);
        $this->entityManager->flush();

        $entity = $this->repository->find($externalLink->getId());

        $this->assertNotNull($entity->getId());
        $this->assertSame(self::NAME, $entity->getName());
        $this->assertSame(self::URL, $entity->getUrl());
    }

    private function buildExternalLink(): ExternalLink
    {
        $externalLink = new ExternalLink();

        $externalLink->setName(self::NAME);
        $externalLink->setUrl(self::URL);

        return $externalLink;
    }
}
