<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Router;

use Exception;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class WebRouter
{
    const ROUTES = [
        ['GET', '/', 'home_controller/index'],
        ['GET', '/posts/page/{page}', 'home_controller/getPosts']
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
                $this->renderError(StatusCode::NOT_FOUND, 'This page does not exist!');
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
        try {
            $controller = $this->container->get($controllerKey);
            $controller->$methodName($vars);
        } catch (HttpException $e) {
            $this->renderError($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            $this->renderError(StatusCode::INTERNAL_SERVER_ERROR, 'Something went wrong!');
        }
    }

    /**
     * @param int $errorCode
     * @param string $errorDescription
     * @throws ContainerExceptionInterface
     */
    private function renderError(int $errorCode, string $errorDescription): void
    {
        /** @var Twig_Environment */
        $twig = $this->container->get('twig');

        $twig->display('error.html.twig', ['errorCode' => $errorCode, 'errorDescription' => $errorDescription]);
    }
}