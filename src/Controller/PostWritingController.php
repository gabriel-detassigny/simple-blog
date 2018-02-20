<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use Twig_Environment;
use Twig_Error;

class PostWritingController
{
    /** @var Twig_Environment */
    private $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws Twig_Error
     */
    public function newPost()
    {
        $this->twig->display('posts/new.twig');
    }
}