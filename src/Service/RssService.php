<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\ValueObject\Page;
use Psr\Http\Message\ServerRequestInterface;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

class RssService
{
    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var PostViewingService */
    private $postViewingService;

    /** @var ServerRequestInterface */
    private $request;

    public function __construct(
        BlogInfoService $blogInfoService,
        PostViewingService $postViewingService,
        ServerRequestInterface $request
    ) {
        $this->blogInfoService = $blogInfoService;
        $this->postViewingService = $postViewingService;
        $this->request = $request;
    }

    public function getRSSFeed(): Feed
    {
        $feed = new Feed();

        $channel = $this->buildChannel();
        $channel->appendTo($feed);

        return $feed;
    }

    private function buildChannel(): Channel
    {
        $channel = new Channel();
        $baseUrl = $this->getBaseUrl();
        $posts = $this->postViewingService->findPageOfLatestPosts(new Page(1));

        $channel
            ->title($this->blogInfoService->getBlogTitle())
            ->description($this->blogInfoService->getBlogDescription())
            ->url($baseUrl)
            ->feedUrl($baseUrl . '/rss')
            ->language('en-US')
            ->ttl(60);

        if (!empty($posts)) {
            $pubDate = $posts->getIterator()->current()->getCreatedAt()->getTimestamp();
            $channel->pubDate($pubDate);
            $channel->lastBuildDate($pubDate);
        }
        $this->buildItems($posts, $channel);

        return $channel;
    }

    /**
     * @param Paginator $posts
     * @param Channel $channel
     * @return void
     */
    private function buildItems(Paginator $posts, Channel $channel): void
    {
        /** @var Post $post */
        foreach ($posts as $post) {
            $item = new Item();
            $item
                ->title($post->getTitle())
                ->description($post->getSubtitle())
                ->contentEncoded($post->getText())
                ->url($this->getBaseUrl() . $post->getUrl())
                ->author($post->getAuthor()->getName())
                ->pubDate($post->getCreatedAt()->getTimestamp())
                ->guid($this->getBaseUrl() . $post->getUrl(), true)
                ->appendTo($channel);
        }
    }

    private function getBaseUrl(): string
    {
        $uri = $this->request->getUri();
        $baseUrl = $uri->getScheme() . '://' . $uri->getHost();
        if ($uri->getPort() !== null) {
            $baseUrl .= ':' . $uri->getPort();
        }
        return $baseUrl;
    }
}