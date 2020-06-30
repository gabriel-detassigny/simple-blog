<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Repository;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;

abstract class RepositoryTestCase extends TestCase
{
    public function createTestEntityManager(): EntityManager
    {
        if (!extension_loaded('pdo_sqlite')) {
            TestCase::markTestSkipped('Extension pdo_sqlite is required.');
        }

        $config = $this->createTestConfiguration();
        $params = ['driver' => 'pdo_sqlite', 'memory' => true];

        $entityManager = EntityManager::create($params, $config);
        $this->updateSchema($entityManager);

        return $entityManager;
    }

    private function updateSchema(EntityManager $entityManager): void
    {
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadata);
    }

    private function createTestConfiguration(): Configuration
    {
        $config = new Configuration();
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyDir(sys_get_temp_dir());
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(__DIR__ . '/../../src/Entity', false));
        $config->setQueryCacheImpl(new ArrayCache());
        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setProxyNamespace('GabrielDeTassigny\Blog\Tests\Proxies');

        return $config;
    }
}