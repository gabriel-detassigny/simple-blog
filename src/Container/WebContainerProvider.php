<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use GabrielDeTassigny\Blog\Container\ServiceDefinition\ServiceDefinitionManager;
use GabrielDeTassigny\Blog\Container\ServiceDefinition\ServiceProviderStrategy;
use GabrielDeTassigny\Blog\Container\ServiceDefinition\YamlConfigStrategy;
use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\EntityManagerProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\RepositoryProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\LogProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServerRequestProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServiceProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\TwigProvider;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\BlogInfo;
use GabrielDeTassigny\Blog\Entity\Comment;
use GabrielDeTassigny\Blog\Entity\ExternalLink;
use GabrielDeTassigny\Blog\Entity\Post;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Parser;

class WebContainerProvider
{
    /** @var string|null */
    private $configPath;

    /** @var ServiceProviderStrategy */
    private $serviceProviderStrategy;

    public function __construct(?string $configPath = null)
    {
        $this->configPath = $configPath;
        $this->serviceProviderStrategy = new ServiceProviderStrategy();
        $this->registerServices();
    }

    /**
     * @return ContainerInterface
     * @throws InvalidContainerConfigException
     */
    public function getContainer(): ContainerInterface
    {
        $strategies = [$this->serviceProviderStrategy];

        if ($this->configPath) {
            $strategies[] = new YamlConfigStrategy(new Parser(), $this->configPath);
        }

        return new Container(new ServiceDefinitionManager(...$strategies));
    }

    public function registerService(string $id, ServiceProvider $serviceProvider): void
    {
        $this->serviceProviderStrategy->registerService($id, $serviceProvider);
    }

    private function getDbParams(): array
    {
        return [
            'driver' => 'pdo_mysql',
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'dbname' => getenv('DB_NAME'),
            'host' => getenv('DB_HOST')
        ];
    }

    private function registerServices(): void
    {
        $this->registerService('server_request', new ServerRequestProvider());
        $this->registerService('twig', new TwigProvider());

        $entityManagerProvider = new EntityManagerProvider($this->getDbParams());
        $this->registerService('entity_manager', $entityManagerProvider);
        $this->registerService('post_repository', new RepositoryProvider($entityManagerProvider, Post::class));
        $this->registerService('blog_info_repository', new RepositoryProvider($entityManagerProvider, BlogInfo::class));
        $this->registerService('external_link_repository', new RepositoryProvider($entityManagerProvider, ExternalLink::class));
        $this->registerService('author_repository', new RepositoryProvider($entityManagerProvider, Author::class));
        $this->registerService('comment_repository', new RepositoryProvider($entityManagerProvider, Comment::class));

        $this->registerService('log', new LogProvider('app-errors'));
    }
}