<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\BlogInfoService;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use Twig\Environment;

class AboutPageController
{
    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var Environment */
    private $twig;

    /** @var ExternalLinkService */
    private $externalLinkService;

    public function __construct(
        Environment $twig,
        BlogInfoService $blogInfoService,
        ExternalLinkService $externalLinkService
    ) {
        $this->twig = $twig;
        $this->blogInfoService = $blogInfoService;
        $this->externalLinkService = $externalLinkService;
    }

    public function showAboutPage(): void
    {
        $this->twig->display('about.twig', [
            'aboutText' => $this->blogInfoService->getAboutText(),
            'externalLinks' => $this->externalLinkService->getExternalLinks(),
            'blogTitle' => $this->blogInfoService->getBlogTitle()
        ]);
    }
}