<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Router;

use GabrielDeTassigny\Blog\Controller\HomeController;
use GabrielDeTassigny\Blog\Router\WebRouter;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
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

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = Phake::mock(ContainerInterface::class);
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->serverRequest = Phake::mock(ServerRequestInterface::class);
        Phake::when($this->container)->get('server_request')->thenReturn($this->serverRequest);
        Phake::when($this->container)->get('twig')->thenReturn($this->twig);

        $this->router = new WebRouter($this->container);
    }

    public function testDispatchRouteNotFound()
    {
        Phake::when($this->serverRequest)->getMethod()->thenReturn('GET');
        $uri = Phake::mock(UriInterface::class);
        Phake::when($this->serverRequest)->getUri()->thenReturn($uri);
        Phake::when($uri)->getPath()->thenReturn('/unknown-route');

        $this->router->dispatch();

        Phake::verify($this->twig)->display('error.html.twig', Phake::ignoreRemaining());
    }

    public function testDispatchRouteFound()
    {
        Phake::when($this->serverRequest)->getMethod()->thenReturn('GET');
        $uri = Phake::mock(UriInterface::class);
        Phake::when($this->serverRequest)->getUri()->thenReturn($uri);
        Phake::when($uri)->getPath()->thenReturn('/');
        $controller = Phake::mock(HomeController::class);
        Phake::when($this->container)->get('home_controller')->thenReturn($controller);

        $this->router->dispatch();

        Phake::verify($controller)->index([]);
    }
}
