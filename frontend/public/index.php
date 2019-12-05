<?php

use GabrielDeTassigny\Blog\Container\WebContainerProvider;
use GabrielDeTassigny\Blog\Router\RouteParser;
use GabrielDeTassigny\Blog\Router\WebRouter;
use Symfony\Component\Yaml\Parser;

require_once __DIR__ . '/../../bootstrap.php';

if (!defined('PROJECT_DIR')) {
    define('PROJECT_DIR', __DIR__ . '/../../');
}

$yamlParser = new Parser();
$containerProvider = new WebContainerProvider($yamlParser, PROJECT_DIR . 'config/container.yaml');

$router = new WebRouter(
    $containerProvider->getContainer(),
    new RouteParser($yamlParser),
    PROJECT_DIR . 'config/routes.yaml'
);

$router->dispatch();
