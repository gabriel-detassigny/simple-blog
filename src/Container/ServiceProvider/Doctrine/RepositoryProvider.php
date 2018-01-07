<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceProvider;
use Psr\Container\ContainerInterface;

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
     *
     * TODO: Catch possible exceptions
     */
    public function getService()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->container->get('entity_manager');

        return $entityManager->getRepository($this->entityClassName);
    }
}