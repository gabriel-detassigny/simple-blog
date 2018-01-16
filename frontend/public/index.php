<?php

use GabrielDeTassigny\Blog\Container\WebContainerProvider;
use GabrielDeTassigny\Blog\Router\WebRouter;

require_once __DIR__ . '/../../bootstrap.php';

$router = new WebRouter(WebContainerProvider::getContainer());
$router->dispatch();
