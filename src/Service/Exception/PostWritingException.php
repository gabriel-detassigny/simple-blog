<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Exception;

use InvalidArgumentException;

class PostWritingException extends InvalidArgumentException
{
    public const DB_ERROR = 1;
    public const TITLE_ERROR = 2;
    public const TEXT_ERROR = 3;
    public const AUTHOR_ERROR = 4;
    public const STATE_ERROR = 5;
    public const COMMENT_TYPE_ERROR = 6;
}