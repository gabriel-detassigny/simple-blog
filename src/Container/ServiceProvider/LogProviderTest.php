<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container\ServiceProvider;

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
