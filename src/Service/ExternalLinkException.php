<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use RuntimeException;

class ExternalLinkException extends RuntimeException
{
    public const DELETE_ERROR = 1;
    public const CREATE_ERROR = 2;
}