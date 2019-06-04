<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Exception;

use RuntimeException;

class CommentException extends RuntimeException
{
    const FIELD_ERROR = 1;
    const DB_ERROR = 2;
}