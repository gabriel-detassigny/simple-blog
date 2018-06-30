<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Renderer;

use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use PHPUnit\Framework\TestCase;

class JsonRendererTest extends TestCase
{
    const VALUES = ['key' => 'value'];

    public function testRender()
    {
        $renderer = new JsonRenderer();
        ob_start();

        $renderer->render(self::VALUES);

        $json = ob_get_clean();
        $this->assertSame(self::VALUES, json_decode($json, true));
    }
}
