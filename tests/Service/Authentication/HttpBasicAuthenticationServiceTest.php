<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service\Authentication;

use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\Authentication\HttpBasicAuthenticationService;
use PHPUnit\Framework\TestCase;

class HttpBasicAuthenticationServiceTest extends TestCase
{
    /** @var AdminAuthenticator */
    private $service;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->service = new HttpBasicAuthenticationService();
        putenv('ADMIN_USER=user');
        putenv('ADMIN_PASSWORD=password');
    }

    /**
     * @param string $user
     * @param string $password
     * @param bool $expected
     * @dataProvider loginCredentials
     */
    public function testAuthenticateAsAdmin(string $user, string $password, bool $expected)
    {
        $_SERVER['PHP_AUTH_USER'] = $user;
        $_SERVER['PHP_AUTH_PW'] = $password;

        $result = $this->service->authenticateAsAdmin();

        $this->assertSame($expected, $result);
    }

    public function loginCredentials(): array
    {
        return [
            ['user', 'password', true],
            ['user', 'wrong_password', false],
            ['wrong_user', 'password', false]
        ];
    }
}
