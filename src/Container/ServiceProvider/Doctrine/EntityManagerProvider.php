<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceProvider;

class EntityManagerProvider implements ServiceProvider
{

    /**
     * @return EntityManager
     */
    public function getService()
    {
        $paths = [__DIR__ . '/../../Entity'];
        $isDev = filter_var(getenv('DB_DEV'), FILTER_VALIDATE_BOOLEAN);

        $dbParams = [
            'driver' => 'pdo_mysql',
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'dbname' => getenv('DB_NAME')
        ];

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDev);

        return EntityManager::create($dbParams, $config);
    }
}