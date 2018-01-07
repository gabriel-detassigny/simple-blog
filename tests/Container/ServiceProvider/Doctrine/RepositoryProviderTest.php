<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container\ServiceProvider\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\RepositoryProvider;
use Phake;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class RepositoryProviderTest extends TestCase
{
    const ENTITY_NAME = 'entity_name';

    public function testGetService()
    {
        $container = Phake::mock(ContainerInterface::class);
        $entityManager = Phake::mock(EntityManager::class);
        $repository = Phake::mock(EntityRepository::class);

        Phake::when($container)->get('entity_manager')->thenReturn($entityManager);
        Phake::when($entityManager)->getRepository(self::ENTITY_NAME)->thenReturn($repository);

        $serviceProvider = new RepositoryProvider($container, self::ENTITY_NAME);

        $this->assertSame($repository, $serviceProvider->getService());
    }
}
