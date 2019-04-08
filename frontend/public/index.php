<?php

use GabrielDeTassigny\Blog\Container\WebContainerProvider;
use GabrielDeTassigny\Blog\Router\WebRouter;
use Symfony\Component\Yaml\Parser;

require_once __DIR__ . '/../../bootstrap.php';

$containerProvider = new WebContainerProvider(new Parser(), __DIR__ . '/../../config/container.yaml');
$container = $containerProvider->getContainer();
$router = new WebRouter($container);
$router->dispatch();
