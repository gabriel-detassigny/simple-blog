<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct("Could not find an entry for identifier {$id}");
    }
}