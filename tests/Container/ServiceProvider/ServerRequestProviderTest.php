<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container\ServiceProvider;

use GabrielDeTassigny\Blog\Container\ServiceProvider\ServerRequestProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestProviderTest extends TestCase
{
    public function testGetService()
    {
        $serviceProvider = new ServerRequestProvider();

        $this->assertInstanceOf(ServerRequestInterface::class, $serviceProvider->getService());
    }
}
