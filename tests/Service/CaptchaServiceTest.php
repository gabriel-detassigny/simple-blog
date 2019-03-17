<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use GabrielDeTassigny\Blog\Service\CaptchaService;
use GabrielDeTassigny\Blog\Service\SessionService;
use Gregwar\Captcha\CaptchaBuilder;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class CaptchaServiceTest extends TestCase
{
    private const CAPTCHA_TEXT = 'ABC123';
    private const INLINE_IMG = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD';

    /** @var CaptchaBuilder|Phake_IMock */
    private $captchaBuilder;

    /** @var SessionService|Phake_IMock */
    private $sessionService;

    /** @var CaptchaService */
    private $captchaService;

    protected function setUp()
    {
        $this->captchaBuilder = Phake::mock(CaptchaBuilder::class);
        $this->sessionService = Phake::mock(SessionService::class);
        $this->captchaService = new CaptchaService($this->captchaBuilder, $this->sessionService);
    }

    public function testIsValidCaptcha(): void
    {
        Phake::when($this->sessionService)->get('captcha')->thenReturn(self::CAPTCHA_TEXT);

        $this->assertTrue($this->captchaService->isValidCaptcha(self::CAPTCHA_TEXT));
    }

    public function testIsNotValidCaptcha(): void
    {
        Phake::when($this->sessionService)->get('captcha')->thenReturn(self::CAPTCHA_TEXT);

        $this->assertFalse($this->captchaService->isValidCaptcha('invalid'));
    }

    public function testGenerateInlineCaptcha(): void
    {
        Phake::when($this->captchaBuilder)->getPhrase()->thenReturn(self::CAPTCHA_TEXT);
        Phake::when($this->captchaBuilder)->inline()->thenReturn(self::INLINE_IMG);

        $inline = $this->captchaService->generateInlineCaptcha();

        $this->assertSame(self::INLINE_IMG, $inline);
        Phake::verify($this->sessionService)->set('captcha', self::CAPTCHA_TEXT);
    }
}
