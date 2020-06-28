<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigProvider implements ServiceProvider
{
    /**
     * @return Environment
     */
    public function getService()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../../frontend/views/');

        $enableCache = filter_var(getenv('TWIG_CACHE'), FILTER_VALIDATE_BOOLEAN);
        $enableDebug = filter_var(getenv('TWIG_DEBUG'), FILTER_VALIDATE_BOOLEAN);

        return new Environment($loader, array(
            'cache' => ($enableCache ? __DIR__ . '/../../../cache' : false),
            'debug' => $enableDebug
        ));
    }
}