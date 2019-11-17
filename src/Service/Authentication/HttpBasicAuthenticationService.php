<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Authentication;

use function Saffyre\basicHttpAuth;

class HttpBasicAuthenticationService implements AdminAuthenticator
{
    private const LOGIN_MESSAGE = "Please log in.";

    public function authenticateAsAdmin(): bool
    {
        return basicHttpAuth(self::LOGIN_MESSAGE, function($username, $password) {
            return $username === getenv('ADMIN_USER') && $password === getenv('ADMIN_PASSWORD');
        });
    }
}