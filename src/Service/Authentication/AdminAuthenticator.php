<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Authentication;

interface AdminAuthenticator
{
    public function authenticateAsAdmin(): bool;
}