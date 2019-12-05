<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Router;

use GabrielDeTassigny\Blog\Router\RouteParser;
use GabrielDeTassigny\Blog\Router\RouteParsingException;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class RouteParserTest extends TestCase
{
    private const CONFIG_LOCATION = '/tmp/routes.yaml';

    /** @var RouteParser */
    private $routeParser;

    /** @var Parser|Phake_IMock */
    private $yamlParser;

    public function setUp(): void
    {
        $this->yamlParser = Phake::mock(Parser::class);
        $this->routeParser = new RouteParser($this->yamlParser);
    }

    public function testParseRouteFileThrowsErrorWhenYamlParsingFailed()
    {
        Phake::when($this->yamlParser)->parseFile(self::CONFIG_LOCATION)
            ->thenThrow(new ParseException('Could not parse YAML file'));

        $this->expectException(RouteParsingException::class);

        $this->routeParser->parseRouteFile(self::CONFIG_LOCATION);
    }

    /**
     * @param array $config
     * @param string $expectedMessage
     * @dataProvider invalidConfigProvider
     */
    public function testParseRouteFileThrowsErrorForInvalidConfig(array $config, string $expectedMessage): void
    {
        Phake::when($this->yamlParser)->parseFile(self::CONFIG_LOCATION)
            ->thenReturn($config);

        $this->expectException(RouteParsingException::class);
        $this->expectDeprecationMessage($expectedMessage);

        $this->routeParser->parseRouteFile(self::CONFIG_LOCATION);
    }

    public function invalidConfigProvider(): array
    {
        return [
            'Empty config' => [
                [],
                'No routes defined in routes config'
            ],
            'No HTTP verbs' => [
                ['routes' => ['/' => null]],
                'Invalid HTTP methods in /'
            ],
            'No route definition' => [
                ['routes' => ['/' => ['GET' => null]]],
                'Invalid route definition for GET /'
            ],
            'No method in route definition' => [
                ['routes' => ['/' => ['GET' => ['controller' => 'foo']]]],
                'Invalid route definition for GET /'
            ],
            'No controller in route definition' => [
                ['routes' => ['/' => ['GET' => ['method' => 'foo']]]],
                'Invalid route definition for GET /'
            ]
        ];
    }

    public function testParseRouteFileReturnsFormattedRoutesFromConfig(): void
    {
        Phake::when($this->yamlParser)->parseFile(self::CONFIG_LOCATION)
            ->thenReturn([
                'routes' => [
                    '/' => [
                        'GET' => ['controller' => 'foo', 'method' => 'foo2'],
                        'POST' => ['controller' => 'bar', 'method' => 'bar2']
                    ],
                    '/foo' => [
                        'PUT' => ['controller' => 'baz', 'method' => 'baz2']
                    ]
                ]
            ]);

        $routes = [
            ['GET', '/', 'foo/foo2'],
            ['POST', '/', 'bar/bar2'],
            ['PUT', '/foo', 'baz/baz2']
        ];

        $this->assertSame($routes, $this->routeParser->parseRouteFile(self::CONFIG_LOCATION));
    }
}
