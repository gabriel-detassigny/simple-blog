<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use GabrielDeTassigny\Blog\Container\ServiceDefinition\ServiceDefinitionStrategy;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /** @var array */
    private $objects = [];

    /** @var ServiceDefinitionStrategy */
    private $strategy;

    public function __construct(ServiceDefinitionStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (isset($this->objects[$id])) {
            return $this->objects[$id];
        }

        if (!$this->strategy->hasDefinition($id)) {
            throw new NotFoundException($id);
        }

        return $this->getServiceInstance($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return isset($this->objects[$id]) || $this->strategy->hasDefinition($id);
    }

    private function getServiceInstance(string $id): object
    {
        $serviceDefinition = $this->strategy->getDefinition($id);
        $object = $serviceDefinition->getInstance();

        if (!$object) {
            $objectDependencies = $this->getDependenciesInstances($serviceDefinition->getDependencies());
            $className = $serviceDefinition->getName();

            $object = new $className(...$objectDependencies);
        }

        $this->objects[$id] = $object;

        return $this->objects[$id];
    }

    private function getDependenciesInstances(array $dependencyList): array
    {
        $dependencies = [];
        foreach ($dependencyList as $dependencyName) {
            $dependencies[] = $this->get($dependencyName);
        }

        return $dependencies;
    }
}