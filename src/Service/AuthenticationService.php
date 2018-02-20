<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

class AuthenticationService
{
    private const LOGIN_MESSAGE = "Please log in.";

    public function authenticateAsAdmin(): bool
    {
        $success = \Saffyre\basicHttpAuth(self::LOGIN_MESSAGE, function($username, $password) {
            return $username === getenv('ADMIN_USER') && $password === getenv('ADMIN_PASSWORD');
        });

        return $success;
    }
}