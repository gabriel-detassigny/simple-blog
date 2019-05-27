<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\ValueObject;

use GabrielDeTassigny\Blog\ValueObject\InvalidStateException;
use GabrielDeTassigny\Blog\ValueObject\PostState;
use PHPUnit\Framework\TestCase;

class PostStateTest extends TestCase
{
    public function testInvalidStateThrowsException(): void
    {
        $this->expectException(InvalidStateException::class);

        new PostState('invalid');
    }

    /**
     * @param string $state
     * @dataProvider validStateProvider
     */
    public function testGetValue(string $state): void
    {
        $postState = new PostState($state);

        $this->assertSame($state, $postState->getValue());
    }

    public function validStateProvider(): array
    {
        return [
            ['draft'],
            ['published']
        ];
    }
}
