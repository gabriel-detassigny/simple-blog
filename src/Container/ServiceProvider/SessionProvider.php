<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider;

class SessionProvider implements ServiceProvider
{
    /**
     * @return array
     */
    public function getService()
    {
        return $_SESSION;
    }
}