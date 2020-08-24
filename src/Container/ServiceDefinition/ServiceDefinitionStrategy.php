<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceDefinition;

use GabrielDeTassigny\Blog\Container\ContainerException;
use GabrielDeTassigny\Blog\Container\NotFoundException;

interface ServiceDefinitionStrategy
{
    /**
     * @param string $id
     * @return ServiceDefinition
     * @throws NotFoundException
     * @throws ContainerException
     */
    public function getDefinition(string $id): ServiceDefinition;

    /**
     * @param string $id
     * @return bool
     */
    public function hasDefinition(string $id): bool;
}
