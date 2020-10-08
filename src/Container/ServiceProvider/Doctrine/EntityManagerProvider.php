<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Exception;
use GabrielDeTassigny\SimpleContainer\Exception\ServiceCreationException;
use GabrielDeTassigny\SimpleContainer\ServiceProvider;

class EntityManagerProvider implements ServiceProvider
{
    /** @var array */
    private $dbParams;

    /** @var EntityManager|null */
    private $service;

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
    public function getService(): object
    {
        if (!$this->service) {
            $paths = [__DIR__ . '/../../../Entity'];
            $isDev = filter_var(getenv('DB_DEV'), FILTER_VALIDATE_BOOLEAN);
            $proxyDir = __DIR__ . '/../../../../cache';
            $config = Setup::createAnnotationMetadataConfiguration($paths, $isDev, $proxyDir, new ArrayCache(), false);

            try {
                $this->service = EntityManager::create($this->dbParams, $config);
            } catch (Exception $e) {
                throw new ServiceCreationException($e->getMessage());
            }
        }

        return $this->service;
    }
}