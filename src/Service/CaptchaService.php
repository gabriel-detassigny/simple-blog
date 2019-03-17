<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use Gregwar\Captcha\CaptchaBuilder;

class CaptchaService
{
    private const SESSION_KEY = 'captcha';

    /** @var CaptchaBuilder */
    private $captchaBuilder;

    /** @var SessionService */
    private $sessionService;

    public function __construct(CaptchaBuilder $captchaBuilder, SessionService $sessionService)
    {
        $this->captchaBuilder = $captchaBuilder;
        $this->sessionService = $sessionService;
    }

    public function generateInlineCaptcha(): string
    {
        $this->captchaBuilder->build();
        $this->sessionService->set(self::SESSION_KEY, $this->captchaBuilder->getPhrase());

        return $this->captchaBuilder->inline();
    }

    public function isValidCaptcha(string $text): bool
    {
        $captcha = $this->sessionService->get(self::SESSION_KEY);

        return strtolower($captcha) === strtolower($text);
    }
}
