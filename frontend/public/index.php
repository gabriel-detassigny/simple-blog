<?php

use GabrielDeTassigny\Blog\Container\WebContainerProvider;
use GabrielDeTassigny\Blog\Router\RouteParser;
use GabrielDeTassigny\Blog\Router\WebRouter;
use Symfony\Component\Yaml\Parser;

require_once __DIR__ . '/../../bootstrap.php';

if (!defined('PROJECT_DIR')) {
    define('PROJECT_DIR', __DIR__ . '/../../');
}

$containerProvider = new WebContainerProvider(PROJECT_DIR . 'config/container.yaml');

$router = new WebRouter(
    $containerProvider->getContainer(),
    new RouteParser(new Parser()),
    PROJECT_DIR . 'config/routes.yaml'
);

$router->dispatch();
