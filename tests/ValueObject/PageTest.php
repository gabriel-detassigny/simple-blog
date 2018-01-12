<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\ValueObject;

use GabrielDeTassigny\Blog\ValueObject\InvalidPageException;
use GabrielDeTassigny\Blog\ValueObject\Page;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public function testInvalidPage()
    {
        $this->expectException(InvalidPageException::class);

        new Page(0);
    }

    public function testGetValue()
    {
        $page = new Page(1);

        $this->assertSame(1, $page->getValue());
    }
}
