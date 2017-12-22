<?php

namespace GabrielDeTassigny\Blog\Tests\Container;

use GabrielDeTassigny\Blog\Container\Container;
use GabrielDeTassigny\Blog\Container\NotFoundException;
use GabrielDeTassigny\Blog\Controller\HomeController;
use PHPUnit\Framework\TestCase;

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
    }

    public function testHasMethodIsTrue()
    {
        $this->assertTrue($this->container->has('home_controller'));
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
        $controller = $this->container->get('home_controller');

        $this->assertInstanceOf(HomeController::class, $controller);
    }
}
