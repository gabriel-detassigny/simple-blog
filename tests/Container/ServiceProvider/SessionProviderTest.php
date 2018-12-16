<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container\ServiceProvider;

use GabrielDeTassigny\Blog\Container\ServiceProvider\SessionProvider;
use PHPUnit\Framework\TestCase;

class SessionProviderTest extends TestCase
{

    public function testGetService()
    {
        $provider = new SessionProvider();

        $this->assertSame($_SESSION, $provider->getService());
    }
}
