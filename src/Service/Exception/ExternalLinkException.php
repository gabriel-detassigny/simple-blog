<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Exception;

use RuntimeException;

class ExternalLinkException extends RuntimeException
{
    public const FIND_ERROR = 1;
    public const CREATE_ERROR = 2;
}