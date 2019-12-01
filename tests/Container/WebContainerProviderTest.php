<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Container;

use GabrielDeTassigny\Blog\Container\InvalidContainerConfigException;
use GabrielDeTassigny\Blog\Container\WebContainerProvider;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class WebContainerProviderTest extends TestCase
{
    const CONFIG_PATH = __DIR__ . '/../../config/container.yaml';

    /** @var WebContainerProvider */
    private $webContainerProvider;

    /** @var Parser|Phake_IMock */
    private $parser;

    public function setUp(): void
    {
        $this->parser = Phake::mock(Parser::class);
        $this->webContainerProvider = new WebContainerProvider($this->parser, self::CONFIG_PATH);
    }

    public function testFailsToGetContainerWhenConfigFileDoesNotExist(): void
    {
        $this->expectException(InvalidContainerConfigException::class);

        $this->webContainerProvider = new WebContainerProvider($this->parser, __DIR__ . '/invalid.yaml');

        $this->webContainerProvider->getContainer();
    }

    public function testFailsToGetContainerWhenYamlIsInvalid(): void
    {
        $this->expectException(InvalidContainerConfigException::class);

        Phake::when($this->parser)->parse(Phake::anyParameters())->thenThrow(new ParseException('invalid yaml'));

        $this->webContainerProvider->getContainer();
    }

    public function testFailsToGetContainerWhenYamlIsEmpty(): void
    {
        $this->expectException(InvalidContainerConfigException::class);

        Phake::when($this->parser)->parse(Phake::anyParameters())->thenReturn([]);

        $this->webContainerProvider->getContainer();
    }

    public function testGetContainerFromConfig(): void
    {
        Phake::when($this->parser)->parse(Phake::anyParameters())->thenReturn(['dependencies' => []]);

        $container = $this->webContainerProvider->getContainer();

        $this->assertInstanceOf(ContainerInterface::class, $container);
    }
}
