<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use ArrayIterator;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\PostViewingService;
use GabrielDeTassigny\Blog\Service\RssService;
use GabrielDeTassigny\Blog\ValueObject\Page;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class RssServiceTest extends TestCase
{
    const EXPECTED_XML = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
  <channel>
    <title>Title</title>
    <link>http://localhost:8000</link>
    <description>Description</description>
    <atom:link xmlns:atom="http://www.w3.org/2005/Atom" href="http://localhost:8000/rss" type="application/rss+xml" rel="self"/>
    <language>en-US</language>
    <pubDate>Sat, 01 Jan 2000 00:00:00 +0000</pubDate>
    <lastBuildDate>Sat, 01 Jan 2000 00:00:00 +0000</lastBuildDate>
    <ttl>60</ttl>
    <item>
      <title>Title</title>
      <link>http://localhost:8000/posts/1/title</link>
      <description>Subtitle</description>
      <content:encoded><![CDATA[<p>Text</p>]]></content:encoded>
      <guid>http://localhost:8000/posts/1/title</guid>
      <pubDate>Sat, 01 Jan 2000 00:00:00 +0000</pubDate>
      <author>Author</author>
    </item>
  </channel>
</rss>

XML;

    /** @var BlogInfoService|Phake_IMock */
    private $blogInfoService;

    /** @var PostViewingService|Phake_IMock */
    private $postViewingService;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /** @var RssService */
    private $rssService;

    public function setUp(): void
    {
        parent::setUp();
        $this->blogInfoService = Phake::mock(BlogInfoService::class);
        $this->postViewingService = Phake::mock(PostViewingService::class);
        $this->request = Phake::mock(ServerRequestInterface::class);

        $this->rssService = new RssService($this->blogInfoService, $this->postViewingService, $this->request);
    }

    public function testGetRSSFeed(): void
    {
        $this->mockRequestUri();
        $this->mockPostsPage();
        $this->mockBlogInfo();

        $feed = $this->rssService->getRSSFeed();

        $this->assertSame(self::EXPECTED_XML, $feed->render());
    }

    private function mockRequestUri(): void
    {
        $uri = Phake::mock(UriInterface::class);
        Phake::when($this->request)->getUri()->thenReturn($uri);
        Phake::when($uri)->getScheme()->thenReturn('http');
        Phake::when($uri)->getHost()->thenReturn('localhost');
        Phake::when($uri)->getPort()->thenReturn(8000);
    }

    private function mockPostsPage(): void
    {
        $post = $this->mockPost();

        $page = Phake::mock(Paginator::class);
        $iterator = new ArrayIterator([$post]);
        Phake::when($page)->getIterator()->thenReturn($iterator);
        Phake::when($this->postViewingService)->findPageOfLatestPosts(new Page(1))->thenReturn($page);
    }

    private function mockPost(): Post
    {
        $post = Phake::mock(Post::class);
        Phake::when($post)->getTitle()->thenReturn('Title');
        Phake::when($post)->getSubtitle()->thenReturn('Subtitle');
        Phake::when($post)->getText()->thenReturn('<p>Text</p>');
        Phake::when($post)->getUrl()->thenReturn('/posts/1/title');
        Phake::when($post)->getCreatedAt()->thenReturn(new DateTime('2000-01-01 00:00:00', new DateTimeZone('UTC')));
        $author = Phake::mock(Author::class);
        Phake::when($author)->getName()->thenReturn('Author');
        Phake::when($post)->getAuthor()->thenReturn($author);

        return $post;
    }

    private function mockBlogInfo(): void
    {
        Phake::when($this->blogInfoService)->getBlogTitle()->thenReturn('Title');
        Phake::when($this->blogInfoService)->getBlogDescription()->thenReturn('Description');
    }
}
