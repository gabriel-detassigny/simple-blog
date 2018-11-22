<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\BlogInfoService;
use Twig_Environment;
use Twig_Error;

class AboutPageController
{
    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var Twig_Environment */
    private $twig;

    public function __construct(Twig_Environment $twig, BlogInfoService $blogInfoService)
    {
        $this->twig = $twig;
        $this->blogInfoService = $blogInfoService;
    }

    /**
     * @throws Twig_Error
     */
    public function showAboutPage()
    {
        $aboutText = $this->blogInfoService->getAboutText();
        $this->twig->display('about.twig', ['aboutText' => $aboutText]);
    }
}