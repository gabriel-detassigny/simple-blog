<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container;

use GabrielDeTassigny\Blog\Container\WebContainerProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class WebContainerProviderTest extends TestCase
{
    public function testGetContainer()
    {
        $containerProvider = new WebContainerProvider();

        $this->assertInstanceOf(ContainerInterface::class, $containerProvider->getContainer());
    }
}
