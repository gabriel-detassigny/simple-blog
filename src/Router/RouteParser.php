<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Router;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class RouteParser
{
    /** @var Parser */
    private $yamlParser;

    public function __construct(Parser $yamlParser)
    {
        $this->yamlParser = $yamlParser;
    }

    /**
     * @param string $configLocation
     * @return array
     * @throws RouteParsingException
     */
    public function parseRouteFile(string $configLocation): array
    {
        $routesConfig = $this->getRoutesConfigFromYaml($configLocation);
        $routes = [];

        foreach ($routesConfig as $url => $httpMethods) {
            if (!is_array($httpMethods)) {
                throw new RouteParsingException('Invalid HTTP methods in ' . $url);
            }

            foreach ($httpMethods as $httpMethod => $routeDefinition) {
                if (!is_array($routeDefinition) || !isset($routeDefinition['controller'], $routeDefinition['method'])) {
                    throw new RouteParsingException("Invalid route definition for $httpMethod $url");
                }

                $routes[] = [$httpMethod, $url, $routeDefinition['controller'] . '/' . $routeDefinition['method']];
            }
        }

        return $routes;
    }

    /**
     * @param string $configLocation
     * @return array
     * @throws RouteParsingException
     */
    private function getRoutesConfigFromYaml(string $configLocation): array
    {
        try {
            $config = $this->yamlParser->parseFile($configLocation);
        } catch (ParseException $e) {
            throw new RouteParsingException('Error parsing YAML config: ' . $e->getMessage());
        }

        if (!isset($config['routes']) || !is_array($config['routes'])) {
            throw new RouteParsingException('No routes defined in routes config');
        }

        return $config['routes'];
    }
}