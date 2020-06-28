<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\ValueObject;

class CommentType
{
    public const NONE = 'none';
    public const INTERNAL = 'internal';
    public const LINK = 'link';

    public const VALID_COMMENT_TYPES = [
        self::NONE,
        self::INTERNAL,
        self::LINK
    ];

    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;

        if (!in_array($this->value, self::VALID_COMMENT_TYPES, true)) {
            throw new InvalidCommentTypeException('Invalid comment type ' . $value);
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }
}