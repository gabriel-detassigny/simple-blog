<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\ValueObject;

class Page
{
    /** @var int */
    private $value;

    /**
     * @param int $value
     * @throws InvalidPageException
     */
    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new InvalidPageException('Page value must be an integer >= to 1');
        }
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}