<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container\ServiceProvider\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use GabrielDeTassigny\Blog\Container\ContainerException;
use GabrielDeTassigny\Blog\Container\NotFoundException;
use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\RepositoryProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceCreationException;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class RepositoryProviderTest extends TestCase
{
    const ENTITY_NAME = 'entity_name';
    const ENTITY_MANAGER = 'entity_manager';

    /** @var ContainerInterface|Phake_IMock */
    private $container;

    /** @var RepositoryProvider */
    private $serviceProvider;

    public function setUp()
    {
        $this->container = Phake::mock(ContainerInterface::class);
        $this->serviceProvider = new RepositoryProvider($this->container, self::ENTITY_NAME);
    }

    public function testGetService()
    {
        $entityManager = Phake::mock(EntityManager::class);
        $repository = Phake::mock(EntityRepository::class);

        Phake::when($this->container)->get(self::ENTITY_MANAGER)->thenReturn($entityManager);
        Phake::when($entityManager)->getRepository(self::ENTITY_NAME)->thenReturn($repository);

        $this->assertSame($repository, $this->serviceProvider->getService());
    }

    public function testGetServiceWhenContainerThrowsContainerException()
    {
        $this->expectException(ServiceCreationException::class);

        Phake::when($this->container)->get(self::ENTITY_MANAGER)->thenThrow(new ContainerException());

        $this->serviceProvider->getService();
    }

    public function testGetServiceWhenContainerThrowsNotFoundException()
    {
        $this->expectException(ServiceCreationException::class);

        Phake::when($this->container)->get(self::ENTITY_MANAGER)
            ->thenThrow(new NotFoundException(self::ENTITY_MANAGER));

        $this->serviceProvider->getService();
    }
}
