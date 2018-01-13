<?php

declare(strict_types=1);

use GabrielDeTassigny\Blog\Container\WebContainerProvider;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

$container = WebContainerProvider::getContainer();

function dispatcher(string $className, string $methodName, array $vars, ContainerInterface $container)
{
    $controller = $container->get($className);
    $controller->$methodName($vars);
}

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->get( '/', 'home_controller/index');
    $r->get('/test/{id}', 'home_controller/index');
});

/** @var ServerRequestInterface */
$serverRequest = $container->get('server_request');

$routeInfo = $dispatcher->dispatch($serverRequest->getMethod(), $serverRequest->getUri()->getPath());

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404 not found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo '405 method not allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($className, $methodName) = explode('/', $handler);
        dispatcher($className, $methodName, $vars, $container);
        break;
}