<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\ValueObject;

class PostState
{
    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';

    public const VALID_STATES = [
        self::DRAFT,
        self::PUBLISHED
    ];

    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;

        if (!in_array($this->value, self::VALID_STATES, true)) {
            throw new InvalidStateException('Invalid state ' . $value);
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }
}