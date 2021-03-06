<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller\Admin;

use DateTime;
use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\ImageService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;

class ImageController extends AbstractAdminController
{
    /** @var AdminAuthenticator */
    private $authenticationService;

    /** @var ServerRequestInterface */
    private $request;

    /** @var JsonRenderer */
    private $renderer;

    /** @var ImageService */
    private $imageService;

    public function __construct(
        AdminAuthenticator $authenticationService,
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

        if (isset($uploadedFiles['file'])) {
            $location = $this->imageService->uploadImage($uploadedFiles['file'], new DateTime());
            $this->renderer->render(['location' => $location]);
        }
    }

    protected function getAdminAuthenticator(): AdminAuthenticator
    {
        return $this->authenticationService;
    }
}