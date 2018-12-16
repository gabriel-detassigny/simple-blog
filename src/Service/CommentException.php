<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use RuntimeException;

class CommentException extends RuntimeException
{
    const CAPTCHA_ERROR = 1;
    const FIELD_ERROR = 2;
}