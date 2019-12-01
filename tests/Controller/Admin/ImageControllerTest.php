<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Controller\Admin;

use GabrielDeTassigny\Blog\Controller\Admin\ImageController;
use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\ImageService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Teapot\HttpException;
use Teapot\StatusCode;

class ImageControllerTest extends TestCase
{
    private const IMAGE_LOCATION = '/images/upload/test.png';

    /** @var AdminAuthenticator|Phake_IMock */
    private $authenticationService;

    /** @var ServerRequestInterface|Phake_IMock */
    private $request;

    /** @var JsonRenderer|Phake_IMock */
    private $renderer;

    /** @var ImageService|Phake_IMock */
    private $imageService;

    /** @var ImageController */
    private $controller;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->authenticationService = Phake::mock(AdminAuthenticator::class);
        $this->request = Phake::mock(ServerRequestInterface::class);
        $this->renderer = Phake::mock(JsonRenderer::class);
        $this->imageService = Phake::mock(ImageService::class);
        $this->controller = new ImageController(
            $this->authenticationService,
            $this->request,
            $this->renderer,
            $this->imageService
        );
    }

    public function testUpload_AuthenticationFailed()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(StatusCode::UNAUTHORIZED);

        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(false);

        $this->controller->upload();
    }

    public function testUpload()
    {
        Phake::when($this->authenticationService)->authenticateAsAdmin()->thenReturn(true);
        $file = Phake::mock(UploadedFileInterface::class);
        Phake::when($this->request)->getUploadedFiles()->thenReturn(['file' => $file]);
        Phake::when($this->imageService)->uploadImage($file, Phake::ignoreRemaining())->thenReturn(self::IMAGE_LOCATION);

        $this->controller->upload();

        Phake::verify($this->renderer)->render(['location' => self::IMAGE_LOCATION]);
    }
}
