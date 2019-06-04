<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Exception;

use RuntimeException;

class AuthorException extends RuntimeException
{
    const FIND_ERROR = 1;
    const CREATE_ERROR = 2;
}