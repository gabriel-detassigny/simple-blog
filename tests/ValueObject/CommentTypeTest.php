<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\ValueObject;

use GabrielDeTassigny\Blog\ValueObject\CommentType;
use GabrielDeTassigny\Blog\ValueObject\InvalidCommentTypeException;
use PHPUnit\Framework\TestCase;

class CommentTypeTest extends TestCase
{

    public function testInvalidCommentTypeThrowsException(): void
    {
        $this->expectException(InvalidCommentTypeException::class);

        new CommentType('invalid');
    }

    /**
     * @dataProvider validCommentTypeProvider
     * @param string $value
     */
    public function testGetValue(string $value): void
    {
        $commentType = new CommentType($value);

        $this->assertSame($value, $commentType->getValue());
    }

    public function validCommentTypeProvider(): array
    {
        return [
            ['none'],
            ['internal'],
            ['link']
        ];
    }
}
