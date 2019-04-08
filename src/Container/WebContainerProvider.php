<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\EntityManagerProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\RepositoryProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\LogProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServerRequestProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\TwigProvider;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\BlogInfo;
use GabrielDeTassigny\Blog\Entity\Comment;
use GabrielDeTassigny\Blog\Entity\ExternalLink;
use GabrielDeTassigny\Blog\Entity\Post;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class WebContainerProvider
{
    /** @var Parser */
    private $yamlParser;

    /** @var string */
    private $configPath;

    public function __construct(Parser $yamlParser, string $configPath)
    {
        $this->yamlParser = $yamlParser;
        $this->configPath = $configPath;
    }

    /**
     * @return ContainerInterface
     * @throws InvalidContainerConfigException
     */
    public function getContainer(): ContainerInterface
    {
        $container = new Container($this->loadDependenciesFromConfig());
        $this->registerServices($container);

        return $container;
    }

    private function loadDependenciesFromConfig(): array
    {
        if (!file_exists($this->configPath)) {
            throw new InvalidContainerConfigException($this->configPath . ': YAML config file not found!');
        }
        try {
            $config = $this->yamlParser->parse(file_get_contents($this->configPath));
        } catch (ParseException $e) {
            throw new InvalidContainerConfigException('Error parsing YAML: ' . $e->getMessage());
        }
        if (!isset($config['dependencies'])) {
            throw new InvalidContainerConfigException('No dependencies found in YAML config');
        }
        return $config['dependencies'];
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

    private function registerServices(Container $container): void
    {
        $container->registerService('server_request', new ServerRequestProvider());
        $container->registerService('twig', new TwigProvider());
        $container->registerService('entity_manager', new EntityManagerProvider($this->getDbParams()));
        $container->registerService('post_repository', new RepositoryProvider($container, Post::class));
        $container->registerService('blog_info_repository', new RepositoryProvider($container, BlogInfo::class));
        $container->registerService('external_link_repository', new RepositoryProvider($container, ExternalLink::class));
        $container->registerService('author_repository', new RepositoryProvider($container, Author::class));
        $container->registerService('comment_repository', new RepositoryProvider($container, Comment::class));
        $container->registerService('log', new LogProvider('app-errors'));
    }
}