<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Exception;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceCreationException;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceProvider;

class EntityManagerProvider implements ServiceProvider
{
    /** @var array */
    private $dbParams;

    /**
     * @param array $dbParams
     */
    public function __construct(array $dbParams)
    {
        $this->dbParams = $dbParams;
    }

    /**
     * @return EntityManager
     * @throws ServiceCreationException
     */
    public function getService()
    {
        $paths = [__DIR__ . '/../../../Entity'];
        $isDev = filter_var(getenv('DB_DEV'), FILTER_VALIDATE_BOOLEAN);
        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDev);

        try {
            return EntityManager::create($this->dbParams, $config);
        } catch (Exception $e) {
            throw new ServiceCreationException($e->getMessage());
        }
    }
}