<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine;

use Doctrine\ORM\EntityRepository;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceCreationException;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceProvider;

class RepositoryProvider implements ServiceProvider
{
    /** @var string */
    private $entityClassName;

    /** @var EntityManagerProvider */
    private $entityManagerProvider;

    public function __construct(EntityManagerProvider $entityManagerProvider, string $entityClassName)
    {
        $this->entityManagerProvider = $entityManagerProvider;
        $this->entityClassName = $entityClassName;
    }

    /**
     * @return EntityRepository
     * @throws ServiceCreationException
     */
    public function getService(): object
    {
        return $this->entityManagerProvider->getService()->getRepository($this->entityClassName);
    }
}