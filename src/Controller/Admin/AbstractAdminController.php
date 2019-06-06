<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller\Admin;

use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use Teapot\HttpException;
use Teapot\StatusCode;

abstract class AbstractAdminController
{
    private const ERROR_MESSAGE = 'Authentication failed';

    abstract protected function getAdminAuthenticator(): AdminAuthenticator;

    /**
     * @throws HttpException
     */
    protected function ensureAdminAuthentication(): void
    {
        if (!$this->getAdminAuthenticator()->authenticateAsAdmin()) {
            throw new HttpException(self::ERROR_MESSAGE, StatusCode::UNAUTHORIZED);
        }
    }
}