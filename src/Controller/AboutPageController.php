<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use Twig\Environment;

class AboutPageController
{
    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var Environment */
    private $twig;

    /** @var AuthorService */
    private $authorService;

    public function __construct(
        Environment $twig,
        BlogInfoService $blogInfoService,
        AuthorService $authorService
    ) {
        $this->twig = $twig;
        $this->blogInfoService = $blogInfoService;
        $this->authorService = $authorService;
    }

    public function showAboutPage(): void
    {
        $this->twig->display('about.twig', [
            'aboutText' => $this->blogInfoService->getAboutText(),
            'authors' => $this->authorService->getAuthors(),
            'blogTitle' => $this->blogInfoService->getBlogTitle()
        ]);
    }
}