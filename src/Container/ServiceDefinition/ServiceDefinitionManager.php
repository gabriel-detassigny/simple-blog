<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceDefinition;

use GabrielDeTassigny\Blog\Container\NotFoundException;

class ServiceDefinitionManager implements ServiceDefinitionStrategy
{
    /** @var ServiceDefinitionStrategy[] */
    private $strategies;

    public function __construct(ServiceDefinitionStrategy ...$strategies)
    {
        $this->strategies = $strategies;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefinition(string $id): ServiceDefinition
    {
        $strategy = $this->findStrategy($id);
        if (!$strategy) {
            throw new NotFoundException('Could not resolve service with ID ' . $id);
        }

        return $strategy->getDefinition($id);
    }

    /**
     * {@inheritDoc}
     */
    public function hasDefinition(string $id): bool
    {
        return $this->findStrategy($id) !== null;
    }

    private function findStrategy(string $id): ?ServiceDefinitionStrategy
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->hasDefinition($id)) {
                return $strategy;
            }
        }

        return null;
    }
}