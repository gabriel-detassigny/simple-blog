<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use GabrielDeTassigny\Blog\Controller\HomeController;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Container implements ContainerInterface
{
    private const DEPENDENCIES = [
        'home_controller' => [
            'name' => HomeController::class,
            'dependencies' => ['twig']
        ],
        'server_request' => [
            'method' => 'createServerRequest'
        ],
        'twig' => [
            'method' => 'createTwig'
        ]
    ];

    /** @var array */
    private $objects = [];

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (isset($objects[$id])) {
            return $objects[$id];
        }
        if (!$this->has($id)) {
            throw new NotFoundException($id);
        }
        return $this->createObject($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return array_key_exists($id, self::DEPENDENCIES);
    }

    private function createServerRequest(): ServerRequestInterface
    {
        return ServerRequest::fromGlobals();
    }

    private function createTwig(): Twig_Environment
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../../frontend/views/');
        $enableCache = filter_var(getenv('TWIG_CACHE'), FILTER_VALIDATE_BOOLEAN);
        $enableDebug = filter_var(getenv('TWIG_DEBUG'), FILTER_VALIDATE_BOOLEAN);

        return new Twig_Environment($loader, array(
            'cache' => ($enableCache ? __DIR__ . '/../../cache' : false),
            'debug' => $enableDebug
        ));
    }

    /**
     * @param string $id
     * @return mixed
     */
    private function createObject($id)
    {
        if (array_key_exists('method', self::DEPENDENCIES[$id])) {
            return $this->createObjectFromMethod($id);
        }
        $dependencies = $this->getDependencies(self::DEPENDENCIES[$id]['dependencies']);
        $className = self::DEPENDENCIES[$id]['name'];
        $this->objects[$id] = new $className(...$dependencies);

        return $this->objects[$id];
    }

    private function getDependencies(array $dependencyList): array
    {
        $dependencies = [];
        foreach ($dependencyList as $dependencyName) {
            $dependencies[] = $this->get($dependencyName);
        }

        return $dependencies;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ContainerException
     */
    private function createObjectFromMethod($id)
    {
        $method = self::DEPENDENCIES[$id]['method'];
        if (!method_exists($this, $method)) {
            throw new ContainerException("Method {$method} not found", ContainerException::UNKNOWN_METHOD);
        }
        $this->objects[$id] = $this->$method();
        return $this->objects[$id];
    }
}