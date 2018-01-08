<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceCreationException;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceProvider;
use GabrielDeTassigny\Blog\Controller\HomeController;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private const DEPENDENCIES = [
        'home_controller' => [
            'name' => HomeController::class,
            'dependencies' => ['twig', 'post_repository']
        ]
    ];

    /** @var array */
    private $objects = [];

    /** @var ServiceProvider[] */
    private $serviceProviders = [];

    /**
     * {@inheritdoc}
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

    public function retrieveService(string $id)
    {
        try {
            $this->objects[$id] = $this->serviceProviders[$id]->getService();
        } catch (ServiceCreationException $e) {
            throw new ContainerException("Error retrieving service {$id} from its provider");
        }

        return $this->objects[$id];
    }

    private function createObject(string $id)
    {
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
}