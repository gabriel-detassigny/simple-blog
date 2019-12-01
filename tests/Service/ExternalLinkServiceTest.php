<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\ExternalLink;
use GabrielDeTassigny\Blog\Repository\ExternalLinkRepository;
use GabrielDeTassigny\Blog\Service\Exception\ExternalLinkException;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class ExternalLinkServiceTest extends TestCase
{
    private const ID = 1;

    /** @var ExternalLinkRepository|Phake_IMock */
    private $externalLinkRepository;

    /** @var ExternalLinkService */
    private $externalLinkService;

    /** @var EntityManager|Phake_IMock */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->externalLinkRepository = Phake::mock(ExternalLinkRepository::class);
        $this->entityManager = Phake::mock(EntityManager::class);
        $this->externalLinkService = new ExternalLinkService($this->externalLinkRepository, $this->entityManager);
    }

    public function testGetExternalLinks(): void
    {
        $entities = $this->getExternalLinksEntities();
        Phake::when($this->externalLinkRepository)->findAll()->thenReturn($entities);

        $result = $this->externalLinkService->getExternalLinks();

        $this->assertSame($entities, $result);
        $this->assertSame('Github', $result[0]->getName());
        $this->assertSame('https://github.com/some-user', $result[0]->getUrl());
    }

    public function testCreateExternalLink(): void
    {
        $entity = $this->externalLinkService->createExternalLink('Github', 'https://github.com/some-user');

        Phake::inOrder(
            Phake::verify($this->entityManager)->persist($entity),
            Phake::verify($this->entityManager)->flush()
        );
    }

    public function testCreateExternalLink_DatabaseError(): void
    {
        $this->expectException(ExternalLinkException::class);
        $this->expectExceptionCode(ExternalLinkException::CREATE_ERROR);

        Phake::when($this->entityManager)->persist(Phake::anyParameters())->thenThrow(new ORMException());

        $this->externalLinkService->createExternalLink('Github', 'https://github.com/some-user');
    }

    public function testDeleteExternalLink(): void
    {
        $entity = $this->createExternalLinkEntity('Github', 'https://github.com/some-user');
        Phake::when($this->externalLinkRepository)->find(self::ID)->thenReturn($entity);

        $this->externalLinkService->deleteExternalLink(self::ID);

        Phake::inOrder(
            Phake::verify($this->entityManager)->remove($entity),
            Phake::verify($this->entityManager)->flush()
        );
    }

    public function testDeleteExternalLink_NotFound(): void
    {
        $this->expectException(ExternalLinkException::class);
        $this->expectExceptionCode(ExternalLinkException::DELETE_ERROR);

        Phake::when($this->externalLinkRepository)->find(self::ID)->thenReturn(null);

        $this->externalLinkService->deleteExternalLink(self::ID);
    }

    public function testDeleteExternalLink_DatabaseError(): void
    {
        $this->expectException(ExternalLinkException::class);
        $this->expectExceptionCode(ExternalLinkException::DELETE_ERROR);

        $entity = $this->createExternalLinkEntity('Github', 'https://github.com/some-user');
        Phake::when($this->externalLinkRepository)->find(self::ID)->thenReturn($entity);
        Phake::when($this->entityManager)->remove($entity)->thenThrow(new ORMException());

        $this->externalLinkService->deleteExternalLink(self::ID);
    }

    private function getExternalLinksEntities(): array
    {
        $values = [
            'Github' => 'https://github.com/some-user',
            'LinkedIn' => 'https://linkedin.com/some-user'
        ];

        $entities = array();
        foreach ($values as $name => $url) {
            $entities[] = $this->createExternalLinkEntity($name, $url);
        }

        return $entities;
    }

    private function createExternalLinkEntity(string $name, string $url): ExternalLink
    {
        $entity = new ExternalLink();
        $entity->setName($name);
        $entity->setUrl($url);

        return $entity;
    }
}
