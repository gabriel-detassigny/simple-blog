<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\RssController;
use GabrielDeTassigny\Blog\Renderer\RssRenderer;
use GabrielDeTassigny\Blog\Service\RssService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Suin\RSSWriter\Feed;

class RssControllerTest extends TestCase
{
    /** @var RssController */
    private $controller;

    /** @var RssService|Phake_IMock */
    private $rssService;

    /** @var RssRenderer|Phake_IMock */
    private $rssRenderer;

    protected function setUp()
    {
        parent::setUp();

        $this->rssService = Phake::mock(RssService::class);
        $this->rssRenderer = Phake::mock(RssRenderer::class);
        $this->controller = new RssController($this->rssService, $this->rssRenderer);
    }

    public function testShowRss()
    {
        $feed = new Feed();
        Phake::when($this->rssService)->getRSSFeed()->thenReturn($feed);

        $this->controller->showRss();

        Phake::verify($this->rssRenderer)->render($feed);
    }
}
