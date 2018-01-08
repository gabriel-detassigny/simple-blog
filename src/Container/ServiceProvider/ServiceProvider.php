<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider;

interface ServiceProvider
{
    /**
     * @return object
     * @throws ServiceCreationException
     */
    public function getService();
}