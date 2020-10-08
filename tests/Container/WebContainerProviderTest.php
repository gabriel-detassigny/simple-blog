<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container;

use GabrielDeTassigny\Blog\Container\WebContainerProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class WebContainerProviderTest extends TestCase
{
    public function testServicesRegistration(): void
    {
        $webContainerProvider = new WebContainerProvider();
        $container = $webContainerProvider->getContainer();

        $this->assertTrue($container->has(ServerRequestInterface::class));
    }
}
