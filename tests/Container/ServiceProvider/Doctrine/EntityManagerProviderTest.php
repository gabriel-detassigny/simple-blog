<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container\ServiceProvider\Doctrine;

use Doctrine\ORM\EntityManager;
use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\EntityManagerProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceCreationException;
use PHPUnit\Framework\TestCase;

class EntityManagerProviderTest extends TestCase
{
    public function testGetService()
    {
        $serviceProvider = new EntityManagerProvider([
            'driver' => 'pdo_sqlite',
            'memory' => true
        ]);
        $service = $serviceProvider->getService();

        $this->assertInstanceOf(EntityManager::class, $service);
    }

    public function testGetServiceThrowsException()
    {
        $this->expectException(ServiceCreationException::class);

        $serviceProvider = new EntityManagerProvider([]);

        $serviceProvider->getService();
    }
}
