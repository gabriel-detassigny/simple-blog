<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Router;

use GabrielDeTassigny\Blog\Router\RouteParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Parser;

class RouteParserTest extends TestCase
{
    private const CONFIG_LOCATION = __DIR__ . '/../../config/routes.yaml';
    private const ROUTES = [
        ['GET', '/', 'post_viewing_controller/index'],
        ['GET', '/posts/page/{page}', 'post_viewing_controller/getPosts'],
        ['GET', '/posts/{id:\d+}/edit', 'post_writing_controller/editPost'],
        ['GET', '/posts/{id:\d+}/{slug}', 'post_viewing_controller/showPost'],
        ['GET', '/posts/new', 'post_writing_controller/newPost'],
        ['POST', '/posts', 'post_writing_controller/createPost'],
        ['POST', '/posts/{id:\d+}', 'post_writing_controller/updatePost'],
        ['POST', '/posts/{id:\d+}/comments', 'comment_controller/createComment'],
        ['GET', '/comments/captcha', 'comment_controller/refreshCaptcha'],
        ['GET', '/admin', 'admin_index_controller/index'],
        ['POST', '/admin/images/upload', 'image_controller/upload'],
        ['GET', '/about', 'about_page_controller/showAboutPage'],
        ['GET', '/admin/info/edit', 'blog_info_controller/edit'],
        ['POST', '/admin/info/update', 'blog_info_controller/update'],
        ['GET', '/admin/posts/{id:\d+}/comments', 'comment_admin_controller/index'],
        ['POST', '/admin/posts/{id:\d+}/comments', 'comment_admin_controller/createComment'],
        ['GET', '/admin/posts/{id:\d+}/preview', 'post_writing_controller/previewPost'],
        ['DELETE', '/admin/comments/{id:\d+}', 'comment_admin_controller/deleteComment'],
        ['GET', '/admin/posts/{id:\d+}/comments/new', 'comment_admin_controller/newComment'],
        ['GET', '/external-links/new', 'external_link_controller/newExternalLink'],
        ['POST', '/external-links', 'external_link_controller/createExternalLink'],
        ['DELETE', '/external-links/{id:\d+}', 'external_link_controller/deleteExternalLink'],
        ['GET', '/authors/new', 'author_controller/newAuthor'],
        ['POST', '/authors', 'author_controller/createAuthor'],
        ['GET', '/rss', 'rss_controller/showRss']
    ];

    /** @var RouteParser */
    private $routeParser;

    /** @var Parser */
    private $yamlParser;

    protected function setUp(): void
    {
        $this->yamlParser = new Parser();
        $this->routeParser = new RouteParser($this->yamlParser);
    }

    public function testParseRouteFile()
    {
        $this->assertSame(self::ROUTES, $this->routeParser->parseRouteFile(self::CONFIG_LOCATION));
    }
}
