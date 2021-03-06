<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Router;

use GabrielDeTassigny\Blog\Controller\PostViewingController;
use GabrielDeTassigny\Blog\Renderer\ErrorRenderer;
use GabrielDeTassigny\Blog\Router\RouteParser;
use GabrielDeTassigny\Blog\Router\WebRouter;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;
use Teapot\HttpException;
use Teapot\StatusCode;

class WebRouterTest extends TestCase
{
    private const ROUTE_CONFIG = '/tmp/routes.yaml';
    private const ROUTES = [
        ['GET', '/', PostViewingController::class . '/index']
    ];

    /** @var ContainerInterface|Phake_IMock */
    private $container;

    /** @var WebRouter */
    private $router;

    /** @var ServerRequestInterface|Phake_IMock */
    private $serverRequest;

    /** @var LoggerInterface|Phake_IMock */
    private $log;

    /** @var ErrorRenderer|Phake_IMock */
    private $errorRenderer;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->container = Phake::mock(ContainerInterface::class);
        $this->serverRequest = Phake::mock(ServerRequestInterface::class);
        $this->log = Phake::mock(LoggerInterface::class);
        $this->errorRenderer = Phake::mock(ErrorRenderer::class);

        Phake::when($this->serverRequest)->getHeader('Accept')->thenReturn([]);
        Phake::when($this->container)->get(ServerRequestInterface::class)->thenReturn($this->serverRequest);
        Phake::when($this->container)->get(ErrorRenderer::class)->thenReturn($this->errorRenderer);
        Phake::when($this->container)->get(LoggerInterface::class)->thenReturn($this->log);

        $routeParser = Phake::mock(RouteParser::class);
        Phake::when($routeParser)->parseRouteFile(self::ROUTE_CONFIG)->thenReturn(self::ROUTES);

        $this->router = new WebRouter($this->container, $routeParser, self::ROUTE_CONFIG);
    }

    public function testDispatchRouteNotFound(): void
    {
        Phake::when($this->serverRequest)->getMethod()->thenReturn('GET');
        $uri = Phake::mock(UriInterface::class);
        Phake::when($this->serverRequest)->getUri()->thenReturn($uri);
        Phake::when($uri)->getPath()->thenReturn('/unknown-route');

        $this->router->dispatch();

        $this->assertErrorRendered(StatusCode::NOT_FOUND, 'This page does not exist!');
    }

    public function testDispatchRouteFound(): void
    {
        $controller = $this->getMockRoutedController();

        $this->router->dispatch();

        Phake::verify($controller)->index([]);
    }

    public function testDispatchControllerThrowsHttpError(): void
    {
        $controller = $this->getMockRoutedController();
        Phake::when($controller)->index([])->thenThrow(new HttpException('page not found', StatusCode::NOT_FOUND));

        $this->router->dispatch();

        $this->assertErrorRendered(StatusCode::NOT_FOUND, 'page not found');
    }

    public function testDispatchControllerThrowsErrorWithJsonResponse(): void
    {
        $controller = $this->getMockRoutedController();
        Phake::when($controller)->index([])->thenThrow(new \Exception('unhandled error!'));
        Phake::when($this->serverRequest)->getHeader('Accept')->thenReturn(['application/json, text/javascript']);

        $this->router->dispatch();

        Phake::verify($this->errorRenderer)->setContentTypeToJson();
        $this->assertErrorRendered(StatusCode::INTERNAL_SERVER_ERROR, 'Something went wrong!');
    }

    private function assertErrorRendered(int $code, string $description): void
    {
        Phake::verify($this->errorRenderer)->renderError($code, $description);
    }

    private function getMockRoutedController(): Phake_IMock
    {
        Phake::when($this->serverRequest)->getMethod()->thenReturn('GET');
        $uri = Phake::mock(UriInterface::class);

        Phake::when($this->serverRequest)->getUri()->thenReturn($uri);
        Phake::when($uri)->getPath()->thenReturn('/');

        $controller = Phake::mock(PostViewingController::class);
        Phake::when($this->container)->get(PostViewingController::class)->thenReturn($controller);

        return $controller;
    }
}
