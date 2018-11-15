<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\EntityManagerProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\RepositoryProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\LogProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServerRequestProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\TwigProvider;
use GabrielDeTassigny\Blog\Entity\Post;
use Psr\Container\ContainerInterface;

class WebContainerProvider
{
    public static function getContainer(): ContainerInterface
    {
        $container = new Container();
        $container->registerService('server_request', new ServerRequestProvider());
        $container->registerService('twig', new TwigProvider());
        $container->registerService('entity_manager', new EntityManagerProvider(self::getDbParams()));
        $container->registerService('post_repository', new RepositoryProvider($container, Post::class));
        $container->registerService('log', new LogProvider('app-errors'));

        return $container;
    }

    private static function getDbParams(): array
    {
        return [
            'driver' => 'pdo_mysql',
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'dbname' => getenv('DB_NAME'),
            'host' => getenv('DB_HOST')
        ];
    }
}