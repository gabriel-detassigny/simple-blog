<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\EntityManagerProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\RepositoryProvider;
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
        $container->registerService('entity_manager', new EntityManagerProvider());
        $container->registerService('post_repository', new RepositoryProvider($container, Post::class));

        return $container;
    }
}