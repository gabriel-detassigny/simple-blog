<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Container;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface
{
    public const UNKNOWN_METHOD = 1;
}