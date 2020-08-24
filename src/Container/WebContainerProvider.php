<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use Doctrine\ORM\EntityManager;
use GabrielDeTassigny\Blog\Container\ServiceDefinition\AutowiringStrategy;
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
use GabrielDeTassigny\Blog\Repository\AuthorRepository;
use GabrielDeTassigny\Blog\Repository\BlogInfoRepository;
use GabrielDeTassigny\Blog\Repository\CommentRepository;
use GabrielDeTassigny\Blog\Repository\ExternalLinkRepository;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Parser;
use Twig\Environment;

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
        $strategies[] = new AutowiringStrategy();

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
        $this->registerService(ServerRequestInterface::class, new ServerRequestProvider());
        $this->registerService(Environment::class, new TwigProvider());

        $entityManagerProvider = new EntityManagerProvider($this->getDbParams());
        $this->registerService(EntityManager::class, $entityManagerProvider);
        $this->registerService(PostRepository::class, new RepositoryProvider($entityManagerProvider, Post::class));
        $this->registerService(BlogInfoRepository::class, new RepositoryProvider($entityManagerProvider, BlogInfo::class));
        $this->registerService(ExternalLinkRepository::class, new RepositoryProvider($entityManagerProvider, ExternalLink::class));
        $this->registerService(AuthorRepository::class, new RepositoryProvider($entityManagerProvider, Author::class));
        $this->registerService(CommentRepository::class, new RepositoryProvider($entityManagerProvider, Comment::class));

        $this->registerService(LoggerInterface::class, new LogProvider('app-errors'));
    }
}