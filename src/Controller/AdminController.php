<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthenticationService;
use Teapot\HttpException;
use Teapot\StatusCode;

abstract class AdminController
{
    private const ERROR_MESSAGE = 'Authentication failed';

    abstract protected function getAuthenticationService(): AuthenticationService;

    /**
     * @throws HttpException
     */
    protected function ensureAdminAuthentication(): void
    {
        if (!$this->getAuthenticationService()->authenticateAsAdmin()) {
            throw new HttpException(self::ERROR_MESSAGE, StatusCode::UNAUTHORIZED);
        }
    }
}