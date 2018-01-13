<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceCreationException;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RepositoryProvider implements ServiceProvider
{
    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $entityClassName;

    public function __construct(ContainerInterface $container, string $entityClassName)
    {
        $this->container = $container;
        $this->entityClassName = $entityClassName;
    }

    /**
     * @return EntityRepository
     * @throws ServiceCreationException
     */
    public function getService()
    {
        try {
            /** @var EntityManager $entityManager */
            $entityManager = $this->container->get('entity_manager');
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceCreationException("Error when attempting to retrieve entity_manager dependency");
        }

        return $entityManager->getRepository($this->entityClassName);
    }
}