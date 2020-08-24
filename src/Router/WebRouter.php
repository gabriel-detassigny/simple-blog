<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Router;

use Exception;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use GabrielDeTassigny\Blog\Renderer\ErrorRenderer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Teapot\HttpException;
use Teapot\StatusCode;

class WebRouter
{
    private const EXCEPTION_MESSAGE = 'An unexpected exception occurred!';

    /** @var ContainerInterface */
    private $container;

    /** @var Dispatcher */
    private $dispatcher;

    public function __construct(ContainerInterface $container, RouteParser $routeParser, string $routeConfig)
    {
        $this->container = $container;

        $routes = $routeParser->parseRouteFile($routeConfig);

        $this->dispatcher = simpleDispatcher(function(RouteCollector $r) use ($routes) {
            foreach ($routes as $route) {
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
        $serverRequest = $this->container->get(ServerRequestInterface::class);

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
            /** @var LoggerInterface $log */
            $log = $this->container->get(LoggerInterface::class);
            $log->error(self::EXCEPTION_MESSAGE, ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

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
        /** @var ErrorRenderer $renderer */
        $renderer = $this->container->get(ErrorRenderer::class);

        if ($this->isJsonExpected()) {
            $renderer->setContentTypeToJson();
        }

        $renderer->renderError($errorCode, $errorDescription);
    }

    private function isJsonExpected(): bool
    {
        /** @var ServerRequestInterface $request */
        $request = $this->container->get(ServerRequestInterface::class);

        foreach ($request->getHeader('Accept') as $acceptedContentType) {
            if (strpos($acceptedContentType, ErrorRenderer::JSON) !== false) {
                return true;
            }
        }

        return false;
    }
}