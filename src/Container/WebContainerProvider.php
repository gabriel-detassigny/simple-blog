<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use Doctrine\ORM\EntityManager;
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
use GabrielDeTassigny\Blog\Repository\AuthorRepository;
use GabrielDeTassigny\Blog\Repository\BlogInfoRepository;
use GabrielDeTassigny\Blog\Repository\CommentRepository;
use GabrielDeTassigny\Blog\Repository\ExternalLinkRepository;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\SimpleContainer\ContainerProvider;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class WebContainerProvider extends ContainerProvider
{
    public function __construct(?string $configPath = null)
    {
        parent::__construct($configPath);
        $this->registerServices();
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