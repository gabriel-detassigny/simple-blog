<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Router;

use Exception;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use GabrielDeTassigny\Blog\Renderer\ErrorRenderer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Teapot\HttpException;
use Teapot\StatusCode;

class WebRouter
{
    private const ROUTES = [
        ['GET', '/', 'post_viewing_controller/index'],
        ['GET', '/posts/page/{page}', 'post_viewing_controller/getPosts'],
        ['GET', '/posts/{id:\d+}/{slug}', 'post_viewing_controller/showPost'],
        ['GET', '/posts/new', 'post_writing_controller/newPost'],
        ['POST', '/posts', 'post_writing_controller/createPost'],
        ['GET', '/posts/{id:\d+}/edit', 'post_writing_controller/editPost'],
        ['POST', '/posts/{id:\d+}', 'post_writing_controller/updatePost'],
        ['GET', '/admin', 'admin_index_controller/index'],
        ['POST', '/admin/images/upload', 'image_controller/upload'],
        ['GET', '/about', 'about_page_controller/showAboutPage'],
        ['GET', '/admin/info/edit', 'blog_info_controller/edit'],
        ['POST', '/admin/info/update', 'blog_info_controller/update'],
        ['GET', '/external-links/new', 'external_link_controller/newExternalLink'],
        ['POST', '/external-links', 'external_link_controller/createExternalLink'],
        ['DELETE', '/external-links/{id:\d+}', 'external_link_controller/deleteExternalLink'],
        ['GET', '/authors/new', 'author_controller/newAuthor'],
        ['POST', '/authors', 'author_controller/createAuthor']
    ];
    private const EXCEPTION_MESSAGE = 'An unexpected exception occurred!';

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
            foreach (self::ROUTES as $route) {
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
            /** @var LoggerInterface $log */
            $log = $this->container->get('log');
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
        $renderer = $this->container->get('error_renderer');
        if ($this->isJsonExpected()) {
            $renderer->setContentTypeToJson();
        }

        $renderer->renderError($errorCode, $errorDescription);
    }

    private function isJsonExpected(): bool
    {
        /** @var ServerRequestInterface $request */
        $request = $this->container->get('server_request');

        foreach ($request->getHeader('Accept') as $acceptedContentType) {
            if (strpos($acceptedContentType, ErrorRenderer::JSON) !== false) {
                return true;
            }
        }
        return false;
    }
}