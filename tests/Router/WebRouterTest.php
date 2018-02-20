<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Router;

use GabrielDeTassigny\Blog\Controller\PostViewingController;
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
use Twig_Environment;

class WebRouterTest extends TestCase
{
    /** @var ContainerInterface|Phake_IMock */
    private $container;

    /** @var WebRouter */
    private $router;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /** @var ServerRequestInterface|Phake_IMock */
    private $serverRequest;

    /** @var LoggerInterface|Phake_IMock */
    private $log;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = Phake::mock(ContainerInterface::class);
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->serverRequest = Phake::mock(ServerRequestInterface::class);
        $this->log = Phake::mock(LoggerInterface::class);

        Phake::when($this->container)->get('server_request')->thenReturn($this->serverRequest);
        Phake::when($this->container)->get('twig')->thenReturn($this->twig);
        Phake::when($this->container)->get('log')->thenReturn($this->log);

        $this->router = new WebRouter($this->container);
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

    public function testDispatchControllerThrowsError(): void
    {
        $controller = $this->getMockRoutedController();
        Phake::when($controller)->index([])->thenThrow(new \Exception('unhandled error!'));

        $this->router->dispatch();

        $this->assertErrorRendered(StatusCode::INTERNAL_SERVER_ERROR, 'Something went wrong!');
    }

    private function assertErrorRendered(int $code, string $description): void
    {
        Phake::verify($this->twig)->display(
            'error.twig',
            ['errorCode' => $code, 'errorDescription' => $description]
        );
    }

    private function getMockRoutedController(): Phake_IMock
    {
        Phake::when($this->serverRequest)->getMethod()->thenReturn('GET');
        $uri = Phake::mock(UriInterface::class);
        Phake::when($this->serverRequest)->getUri()->thenReturn($uri);
        Phake::when($uri)->getPath()->thenReturn('/');
        $controller = Phake::mock(PostViewingController::class);
        Phake::when($this->container)->get('post_viewing_controller')->thenReturn($controller);

        return $controller;
    }
}
