<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use DateTime;
use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\ImageService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;

class ImageController
{
    /** @var AuthenticationService */
    private $authenticationService;

    /** @var ServerRequestInterface */
    private $request;

    /** @var JsonRenderer */
    private $renderer;

    /** @var ImageService */
    private $imageService;

    public function __construct(
        AuthenticationService $authenticationService,
        ServerRequestInterface $request,
        JsonRenderer $renderer,
        ImageService $imageService
    ) {
        $this->authenticationService = $authenticationService;
        $this->request = $request;
        $this->renderer = $renderer;
        $this->imageService = $imageService;
    }

    /**
     * @throws HttpException
     */
    public function upload(): void
    {
        $this->ensureAdminAuthentication();

        $uploadedFiles = $this->request->getUploadedFiles();
        if (!empty($uploadedFiles) && array_key_exists('file', $uploadedFiles)) {
            $location = $this->imageService->uploadImage($uploadedFiles['file'], new DateTime());
            $this->renderer->render(['location' => $location]);
        }
    }

    /**
     * @throws HttpException
     */
    private function ensureAdminAuthentication(): void
    {
        if (!$this->authenticationService->authenticateAsAdmin()) {
            throw new HttpException('Authentication failed', StatusCode::FORBIDDEN);
        }
    }
}