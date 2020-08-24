<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceDefinition;

use ReflectionClass;

class AutowiringStrategy implements ServiceDefinitionStrategy
{
    /**
     * {@inheritDoc}
     */
    public function getDefinition(string $id): ServiceDefinition
    {
        $dependencies = [];

        $reflection = new ReflectionClass($id);
        $constructor = $reflection->getConstructor();

        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $dependencies[] = $parameter->getClass()->getName();
            }
        }

        return new ServiceDefinition($id, $dependencies);
    }

    /**
     * {@inheritDoc}
     */
    public function hasDefinition(string $id): bool
    {
        return class_exists($id);
    }
}