<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceDefinition;

class ServiceDefinition
{
    /** @var string */
    private $name;

    /** @var array */
    private $dependencies = [];

    /** @var object|null */
    private $instance;

    public function __construct(string $name, array $dependencies, ?object $instance = null)
    {
        $this->name = $name;
        $this->dependencies = $dependencies;
        $this->instance = $instance;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getInstance(): ?object
    {
        return $this->instance;
    }
}