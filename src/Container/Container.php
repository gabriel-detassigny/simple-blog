<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use GabrielDeTassigny\Blog\Controller\HomeController;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Repository\PostRepository;
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
            'dependencies' => ['twig', 'post_repository']
        ],
        'server_request' => [
            'method' => 'createServerRequest'
        ],
        'twig' => [
            'method' => 'createTwig'
        ],
        'entity_manager' => [
            'method' => 'createEntityManager'
        ],
        'post_repository' => [
            'method' => 'createPostRepository'
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

    private function createEntityManager(): EntityManager
    {
        $paths = [__DIR__ . '/../Entity'];
        $isDev = filter_var(getenv('DB_DEV'), FILTER_VALIDATE_BOOLEAN);

        $dbParams = [
            'driver' => 'pdo_mysql',
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'dbname' => getenv('DB_NAME')
        ];

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDev);

        return EntityManager::create($dbParams, $config);
    }

    private function createPostRepository(): PostRepository
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get('entity_manager');

        /** @var PostRepository $repository */
        $repository = $entityManager->getRepository(Post::class);

        return $repository;
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