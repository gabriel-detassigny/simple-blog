<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container\ServiceProvider\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\EntityManagerProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\RepositoryProvider;
use GabrielDeTassigny\SimpleContainer\Exception\ContainerException;
use GabrielDeTassigny\SimpleContainer\Exception\ServiceCreationException;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class RepositoryProviderTest extends TestCase
{
    private const ENTITY_NAME = 'entity_name';
    private const ENTITY_MANAGER = 'entity_manager';

    /** @var EntityManagerProvider|Phake_IMock */
    private $entityManagerProvider;

    /** @var RepositoryProvider */
    private $serviceProvider;

    public function setUp(): void
    {
        $this->entityManagerProvider = Phake::mock(EntityManagerProvider::class);
        $this->serviceProvider = new RepositoryProvider($this->entityManagerProvider, self::ENTITY_NAME);
    }

    public function testGetService()
    {
        $entityManager = Phake::mock(EntityManager::class);
        Phake::when($this->entityManagerProvider)->getService()->thenReturn($entityManager);

        $repository = Phake::mock(EntityRepository::class);
        Phake::when($entityManager)->getRepository(self::ENTITY_NAME)->thenReturn($repository);

        $this->assertSame($repository, $this->serviceProvider->getService());
    }
}
