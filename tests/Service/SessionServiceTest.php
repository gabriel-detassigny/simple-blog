<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use GabrielDeTassigny\Blog\Service\SessionService;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    private const VALUE = 'SOME_VALUE';
    private const KEY = 'SOME_KEY';

    public function testSetAndGetValue(): void
    {
        $service = new SessionService();

        $service->set(self::KEY, self::VALUE);

        $this->assertSame(self::VALUE, $service->get(self::KEY));
    }
}
