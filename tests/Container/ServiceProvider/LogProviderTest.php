<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container\ServiceProvider;

use GabrielDeTassigny\Blog\Container\ServiceProvider\LogProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LogProviderTest extends TestCase
{

    public function testGetService()
    {
        $provider = new LogProvider('name');

        $log = $provider->getService();

        $this->assertInstanceOf(LoggerInterface::class, $log);
    }
}
