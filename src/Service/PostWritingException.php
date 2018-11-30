<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use InvalidArgumentException;

class PostWritingException extends InvalidArgumentException
{
    const DB_ERROR = 1;
    const TITLE_ERROR = 2;
    const TEXT_ERROR = 3;
    const AUTHOR_ERROR = 4;
}