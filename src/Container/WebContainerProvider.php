<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\EntityManagerProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\Doctrine\RepositoryProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\LogProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\ServerRequestProvider;
use GabrielDeTassigny\Blog\Container\ServiceProvider\TwigProvider;
use GabrielDeTassigny\Blog\Controller\AboutPageController;
use GabrielDeTassigny\Blog\Controller\AdminIndexController;
use GabrielDeTassigny\Blog\Controller\AuthorController;
use GabrielDeTassigny\Blog\Controller\BlogInfoController;
use GabrielDeTassigny\Blog\Controller\ExternalLinkController;
use GabrielDeTassigny\Blog\Controller\ImageController;
use GabrielDeTassigny\Blog\Controller\PostViewingController;
use GabrielDeTassigny\Blog\Controller\PostWritingController;
use GabrielDeTassigny\Blog\Controller\RssController;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\BlogInfo;
use GabrielDeTassigny\Blog\Entity\ExternalLink;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Renderer\ErrorRenderer;
use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use GabrielDeTassigny\Blog\Renderer\RssRenderer;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use GabrielDeTassigny\Blog\Service\ImageService;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use GabrielDeTassigny\Blog\Service\PostWritingService;
use GabrielDeTassigny\Blog\Service\RssService;
use Psr\Container\ContainerInterface;

class WebContainerProvider
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
                'author_service',
                'post_viewing_service'
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
                'blog_info_service',
                'external_link_service'
            ]
        ],
        'blog_info_controller' => [
            'name' => BlogInfoController::class,
            'dependencies' => ['twig', 'blog_info_service', 'authentication_service', 'server_request']
        ],
        'external_link_controller' => [
            'name' => ExternalLinkController::class,
            'dependencies' => [
                'authentication_service',
                'twig',
                'external_link_service',
                'server_request',
                'error_renderer'
            ]
        ],
        'author_controller' => [
            'name' => AuthorController::class,
            'dependencies' => ['author_service', 'authentication_service', 'twig', 'server_request']
        ],
        'rss_controller' => [
            'name' => RssController::class,
            'dependencies' => ['rss_service', 'rss_renderer']
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
            'dependencies' => ['external_link_repository', 'entity_manager']
        ],
        'author_service' => [
            'name' => AuthorService::class,
            'dependencies' => ['author_repository', 'entity_manager']
        ],
        'rss_service' => [
            'name' => RssService::class,
            'dependencies' => ['blog_info_service', 'post_viewing_service', 'server_request']
        ],
        'json_renderer' => [
            'name' => JsonRenderer::class,
            'dependencies' => []
        ],
        'error_renderer' => [
            'name' => ErrorRenderer::class,
            'dependencies' => ['twig', 'json_renderer']
        ],
        'rss_renderer' => [
            'name' => RssRenderer::class,
            'dependencies' => []
        ]
    ];

    public static function getContainer(): ContainerInterface
    {
        $container = new Container(self::DEPENDENCIES);
        $container->registerService('server_request', new ServerRequestProvider());
        $container->registerService('twig', new TwigProvider());
        $container->registerService('entity_manager', new EntityManagerProvider(self::getDbParams()));
        $container->registerService('post_repository', new RepositoryProvider($container, Post::class));
        $container->registerService('blog_info_repository', new RepositoryProvider($container, BlogInfo::class));
        $container->registerService('external_link_repository', new RepositoryProvider($container, ExternalLink::class));
        $container->registerService('author_repository', new RepositoryProvider($container, Author::class));
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