<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Renderer\RssRenderer;
use GabrielDeTassigny\Blog\Service\RssService;

class RssController
{
    /** @var RssService */
    private $rssService;

    /** @var RssRenderer */
    private $renderer;

    public function __construct(RssService $rssService, RssRenderer $renderer)
    {
        $this->rssService = $rssService;
        $this->renderer = $renderer;
    }

    public function showRss(): void
    {
        $feed = $this->rssService->getRSSFeed();

        $this->renderer->render($feed);
    }
}