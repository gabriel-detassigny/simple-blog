<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Renderer;

use Suin\RSSWriter\Feed;

class RssRenderer
{
    public function render(Feed $feed): void
    {
        echo $feed->render();
    }
}