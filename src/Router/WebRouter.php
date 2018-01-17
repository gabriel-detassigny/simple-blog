<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig_Environment;

class WebRouter
{
    const ROUTES = [
        ['GET', '/', 'home_controller/index']
    ];

    /** @var ContainerInterface */
    private $container;

    /** @var Dispatcher */
    private $dispatcher;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->dispatcher = \FastRoute\simpleDispatcher(function(RouteCollector $r) {
            foreach (WebRouter::ROUTES as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        });
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public function dispatch(): void
    {
        /** @var ServerRequestInterface $serverRequest */
        $serverRequest = $this->container->get('server_request');

        $routeInfo = $this->dispatcher->dispatch($serverRequest->getMethod(), $serverRequest->getUri()->getPath());

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
            case Dispatcher::METHOD_NOT_ALLOWED:
                $this->renderError("404 Page not found", "This page does not exist!");
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                list($controllerKey, $methodName) = explode('/', $handler);
                $this->dispatchToController($controllerKey, $methodName, $vars);
                break;
        }
    }

    /**
     * @param string $controllerKey
     * @param string $methodName
     * @param array $vars
     * @throws ContainerExceptionInterface
     */
    private function dispatchToController(string $controllerKey, string $methodName, array $vars): void
    {
        $controller = $this->container->get($controllerKey);
        $controller->$methodName($vars);
    }

    /**
     * @param string $errorTitle
     * @param string $errorDescription
     * @throws ContainerExceptionInterface
     */
    private function renderError(string $errorTitle, string $errorDescription): void
    {
        /** @var Twig_Environment */
        $twig = $this->container->get('twig');

        $twig->display('error.html.twig', ['errorTitle' => $errorTitle, 'errorDescription' => $errorDescription]);
    }
}