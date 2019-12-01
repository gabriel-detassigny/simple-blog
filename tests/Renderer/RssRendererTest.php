<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Renderer;

use GabrielDeTassigny\Blog\Renderer\RssRenderer;
use Phake;
use PHPUnit\Framework\TestCase;
use Suin\RSSWriter\Feed;

class RssRendererTest extends TestCase
{
    private const RSS_OUTPUT = '<?xml version="1.0" encoding="UTF-8"?><rss></rss>';

    /** @var RssRenderer */
    private $renderer;

    public function setUp(): void
    {
        $this->renderer = new RssRenderer();
    }

    public function testRender()
    {
        $feed = Phake::mock(Feed::class);
        Phake::when($feed)->render()->thenReturn(self::RSS_OUTPUT);
        ob_start();

        $this->renderer->render($feed);

        $rss = ob_get_clean();
        $this->assertSame(self::RSS_OUTPUT, $rss);
    }
}
