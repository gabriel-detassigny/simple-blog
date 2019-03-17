<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

class SessionService
{
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $_SESSION[$key];
    }
}
