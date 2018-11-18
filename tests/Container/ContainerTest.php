<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container;

use GabrielDeTassigny\Blog\Container\Container;
use GabrielDeTassigny\Blog\Container\ContainerException;
use GabrielDeTassigny\Blog\Container\NotFoundException;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceCreationException;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceProvider;
use GabrielDeTassigny\Blog\Controller\PostViewingController;
use GabrielDeTassigny\Blog\Repository\BlogInfoRepository;
use GabrielDeTassigny\Blog\Repository\ExternalLinkRepository;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use Phake;
use PHPUnit\Framework\TestCase;
use Twig_Environment;

class ContainerTest extends TestCase
{
    /** @var Container */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = new Container();
        $mockTwigProvider = Phake::mock(ServiceProvider::class);
        Phake::when($mockTwigProvider)->getService()->thenReturn(Phake::mock(Twig_Environment::class));
        $this->container->registerService('twig', $mockTwigProvider);

        $mockPostRepositoryProvider = Phake::mock(ServiceProvider::class);
        Phake::when($mockPostRepositoryProvider)->getService()->thenReturn(Phake::mock(PostRepository::class));
        $this->container->registerService('post_repository', $mockPostRepositoryProvider);

        $mockBlogInfoRepositoryProvider = Phake::mock(ServiceProvider::class);
        Phake::when($mockBlogInfoRepositoryProvider)->getService()->thenReturn(Phake::mock(BlogInfoRepository::class));
        $this->container->registerService('blog_info_repository', $mockBlogInfoRepositoryProvider);

        $mockExternalLinksRepositoryProvider = Phake::mock(ServiceProvider::class);
        $externalLinkRepository = Phake::mock(ExternalLinkRepository::class);
        Phake::when($mockExternalLinksRepositoryProvider)->getService()->thenReturn($externalLinkRepository);
        $this->container->registerService('external_link_repository', $mockExternalLinksRepositoryProvider);
    }

    public function testHasMethodIsTrue()
    {
        $this->assertTrue($this->container->has('post_viewing_controller'));
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
        $controller = $this->container->get('post_viewing_controller');

        $this->assertInstanceOf(PostViewingController::class, $controller);
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
        $controller = $this->container->get('post_viewing_controller');

        $this->assertSame($controller, $this->container->get('post_viewing_controller'));
    }
}
