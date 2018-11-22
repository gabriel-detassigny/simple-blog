<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use Twig_Environment;
use Twig_Error;

class AboutPageController
{
    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var Twig_Environment */
    private $twig;

    /** @var ExternalLinkService */
    private $externalLinkService;

    public function __construct(
        Twig_Environment $twig,
        BlogInfoService $blogInfoService,
        ExternalLinkService $externalLinkService
    ) {
        $this->twig = $twig;
        $this->blogInfoService = $blogInfoService;
        $this->externalLinkService = $externalLinkService;
    }

    /**
     * @throws Twig_Error
     */
    public function showAboutPage(): void
    {
        $aboutText = $this->blogInfoService->getAboutText();
        $externalLinks = $this->externalLinkService->getExternalLinks();
        $this->twig->display('about.twig', ['aboutText' => $aboutText, 'externalLinks' => $externalLinks]);
    }
}