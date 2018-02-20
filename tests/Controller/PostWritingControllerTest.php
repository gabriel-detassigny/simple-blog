<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller;

use GabrielDeTassigny\Blog\Controller\PostWritingController;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Twig_Environment;

class PostWritingControllerTest extends TestCase
{
    /** @var PostWritingController */
    private $controller;

    /** @var Twig_Environment|Phake_IMock */
    private $twig;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->twig = Phake::mock(Twig_Environment::class);
        $this->controller = new PostWritingController($this->twig);
    }

    public function testNewPost()
    {
        $this->controller->newPost();

        Phake::verify($this->twig)->display('posts/new.twig');
    }
}
