<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider;

use Twig_Environment;
use Twig_Loader_Filesystem;

class TwigProvider implements ServiceProvider
{
    /**
     * @return Twig_Environment
     */
    public function getService()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../../../frontend/views/');
        $enableCache = filter_var(getenv('TWIG_CACHE'), FILTER_VALIDATE_BOOLEAN);
        $enableDebug = filter_var(getenv('TWIG_DEBUG'), FILTER_VALIDATE_BOOLEAN);

        return new Twig_Environment($loader, array(
            'cache' => ($enableCache ? __DIR__ . '/../../../cache' : false),
            'debug' => $enableDebug
        ));
    }
}