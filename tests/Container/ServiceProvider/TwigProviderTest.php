<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container\ServiceProvider;

use GabrielDeTassigny\Blog\Container\ServiceProvider\TwigProvider;
use PHPUnit\Framework\TestCase;
use Twig_Environment;

class TwigProviderTest extends TestCase
{
    public function testGetService()
    {
        $serviceProvider = new TwigProvider();

        $this->assertInstanceOf(Twig_Environment::class, $serviceProvider->getService());
    }
}
