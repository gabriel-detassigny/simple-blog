<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container;

use GabrielDeTassigny\Blog\Container\Container;
use GabrielDeTassigny\Blog\Container\ContainerException;
use GabrielDeTassigny\Blog\Container\NotFoundException;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceCreationException;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceProvider;
use GabrielDeTassigny\Blog\Renderer\ErrorRenderer;
use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use Phake;
use PHPUnit\Framework\TestCase;
use Twig_Environment;

class ContainerTest extends TestCase
{
    /** @var Container */
    private $container;

    private const DEPENDENCIES = [
        'json_renderer' => [
            'name' => JsonRenderer::class,
            'dependencies' => []
        ],
        'error_renderer' => [
            'name' => ErrorRenderer::class,
            'dependencies' => ['twig', 'json_renderer']
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = new Container(self::DEPENDENCIES);
        $mockTwigProvider = Phake::mock(ServiceProvider::class);
        Phake::when($mockTwigProvider)->getService()->thenReturn(Phake::mock(Twig_Environment::class));
        $this->container->registerService('twig', $mockTwigProvider);
    }

    public function testHasMethodIsTrue()
    {
        $this->assertTrue($this->container->has('json_renderer'));
    }

    public function testHasMethodIsFalse()
    {
        $this->assertFalse($this->container->has('non_existing_entry'));
    }

    public function testGetNotFoundIdentifier()
    {
        $this->expectException(NotFoundException::class);

        $this->container->get('non_existing_entry');
    }

    public function testGetFromIdentifier()
    {
        $controller = $this->container->get('error_renderer');

        $this->assertInstanceOf(ErrorRenderer::class, $controller);
    }

    public function testHasRegisteredService()
    {
        $this->assertTrue($this->container->has('twig'));
    }

    public function testServiceProviderThrowsException()
    {
        $this->expectException(ContainerException::class);

        $serviceProvider = Phake::mock(ServiceProvider::class);
        Phake::when($serviceProvider)->getService()->thenThrow(new ServiceCreationException());

        $this->container->registerService('test_service', $serviceProvider);

        $this->container->get('test_service');
    }

    public function testGetSameObjectOnSuccessiveCalls()
    {
        $controller = $this->container->get('json_renderer');

        $this->assertSame($controller, $this->container->get('json_renderer'));
    }
}
