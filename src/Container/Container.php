<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceCreationException;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceProvider;
use GabrielDeTassigny\Blog\Controller\AboutPageController;
use GabrielDeTassigny\Blog\Controller\AdminIndexController;
use GabrielDeTassigny\Blog\Controller\BlogInfoController;
use GabrielDeTassigny\Blog\Controller\ImageController;
use GabrielDeTassigny\Blog\Controller\PostViewingController;
use GabrielDeTassigny\Blog\Controller\PostWritingController;
use GabrielDeTassigny\Blog\Renderer\ErrorRenderer;
use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use GabrielDeTassigny\Blog\Service\ImageService;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use GabrielDeTassigny\Blog\Service\PostWritingService;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private const DEPENDENCIES = [
        'post_viewing_controller' => [
            'name' => PostViewingController::class,
            'dependencies' => ['twig', 'post_viewing_service', 'blog_info_service', 'external_link_service']
        ],
        'post_writing_controller' => [
            'name' => PostWritingController::class,
            'dependencies' => [
                'twig',
                'authentication_service',
                'server_request',
                'post_writing_service',
                'author_service'
            ]
        ],
        'image_controller' => [
            'name' => ImageController::class,
            'dependencies' => ['authentication_service', 'server_request', 'json_renderer', 'image_service']
        ],
        'about_page_controller' => [
            'name' => AboutPageController::class,
            'dependencies' => ['twig', 'blog_info_service', 'external_link_service']
        ],
        'admin_index_controller' => [
            'name' => AdminIndexController::class,
            'dependencies' => [
                'twig',
                'authentication_service',
                'post_viewing_service',
                'author_service',
                'blog_info_service'
            ]
        ],
        'blog_info_controller' => [
            'name' => BlogInfoController::class,
            'dependencies' => ['twig', 'blog_info_service', 'authentication_service']
        ],
        'post_viewing_service' => [
            'name' => PostViewingService::class,
            'dependencies' => ['post_repository']
        ],
        'authentication_service' => [
            'name' => AuthenticationService::class,
            'dependencies' => []
        ],
        'post_writing_service' => [
            'name' => PostWritingService::class,
            'dependencies' => ['entity_manager', 'author_service']
        ],
        'image_service' => [
            'name' => ImageService::class,
            'dependencies' => ['log']
        ],
        'blog_info_service' => [
            'name' => BlogInfoService::class,
            'dependencies' => ['blog_info_repository', 'entity_manager']
        ],
        'external_link_service' => [
            'name' => ExternalLinkService::class,
            'dependencies' => ['external_link_repository']
        ],
        'author_service' => [
            'name' => AuthorService::class,
            'dependencies' => ['author_repository']
        ],
        'json_renderer' => [
            'name' => JsonRenderer::class,
            'dependencies' => []
        ],
        'error_renderer' => [
            'name' => ErrorRenderer::class,
            'dependencies' => ['twig', 'json_renderer']
        ]
    ];

    /** @var array */
    private $objects = [];

    /** @var ServiceProvider[] */
    private $serviceProviders = [];

    /**
     * @param string $id
     * @return object
     * @throws NotFoundException
     * @throws ContainerException
     */
    public function get($id)
    {
        if (isset($this->objects[$id])) {
            return $this->objects[$id];
        }
        if (isset($this->serviceProviders[$id])) {
            return $this->retrieveService($id);
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
        return array_key_exists($id, self::DEPENDENCIES) || array_key_exists($id, $this->serviceProviders);
    }

    /**
     * @param string $serviceName
     * @param ServiceProvider $serviceProvider
     * @return void
     */
    public function registerService(string $serviceName, ServiceProvider $serviceProvider): void
    {
        $this->serviceProviders[$serviceName] = $serviceProvider;
    }

    /**
     * @param string $id
     * @return object
     * @throws ContainerException
     */
    private function retrieveService(string $id)
    {
        try {
            $this->objects[$id] = $this->serviceProviders[$id]->getService();
        } catch (ServiceCreationException $e) {
            throw new ContainerException("Error retrieving service {$id} from its provider");
        }

        return $this->objects[$id];
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ContainerException
     */
    private function createObject(string $id)
    {
        $dependencies = $this->getDependencies(self::DEPENDENCIES[$id]['dependencies']);
        $className = self::DEPENDENCIES[$id]['name'];
        $this->objects[$id] = new $className(...$dependencies);

        return $this->objects[$id];
    }

    /**
     * @param array $dependencyList
     * @return array
     * @throws ContainerException
     */
    private function getDependencies(array $dependencyList): array
    {
        $dependencies = [];
        foreach ($dependencyList as $dependencyName) {
            $dependencies[] = $this->get($dependencyName);
        }

        return $dependencies;
    }
}