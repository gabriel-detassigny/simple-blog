<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller\Admin;

use GabrielDeTassigny\Blog\Controller\Admin\AbstractAdminController;
use GabrielDeTassigny\Blog\Renderer\ErrorRenderer;
use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\Exception\ExternalLinkException;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class ExternalLinkController extends AbstractAdminController
{
    private const SUCCESS_MESSAGE = 'External Link was successfully created!';

    /** @var AdminAuthenticator */
    private $authenticationService;

    /** @var Twig_Environment */
    private $twig;

    /** @var ExternalLinkService */
    private $externalLinkService;

    /** @var ServerRequestInterface */
    private $request;

    /** @var ErrorRenderer */
    private $errorRenderer;

    public function __construct(
        AdminAuthenticator $authenticationService,
        Twig_Environment $twig,
        ExternalLinkService $externalLinkService,
        ServerRequestInterface $request,
        ErrorRenderer $errorRenderer
    ) {
        $this->authenticationService = $authenticationService;
        $this->twig = $twig;
        $this->externalLinkService = $externalLinkService;
        $this->request = $request;
        $this->errorRenderer = $errorRenderer;
    }

    public function newExternalLink(): void
    {
        $this->ensureAdminAuthentication();
        $this->twig->display('external-links/new.twig');
    }

    public function createExternalLink(): void
    {
        $this->ensureAdminAuthentication();
        $body = $this->request->getParsedBody();
        if (!is_array($body) || !array_key_exists('link', $body) || !is_array($body['link'])) {
            throw new HttpException('Invalid form parameters', StatusCode::BAD_REQUEST);
        }
        try {
            $this->externalLinkService->createExternalLink($body['link']['name'], $body['link']['url']);
            $this->twig->display('external-links/new.twig', ['success' => self::SUCCESS_MESSAGE]);
        } catch (ExternalLinkException $e) {
            $this->twig->display('external-links/new.twig', ['error' => $e->getMessage()]);
        }
    }

    public function deleteExternalLink(array $vars): void
    {
        $this->ensureAdminAuthentication();
        try {
            $this->externalLinkService->deleteExternalLink((int) $vars['id']);
        } catch (ExternalLinkException $e) {
            $this->errorRenderer->setContentTypeToJson();
            $this->errorRenderer->renderError(StatusCode::BAD_REQUEST, $e->getMessage());
        }
    }

    protected function getAdminAuthenticator(): AdminAuthenticator
    {
        return $this->authenticationService;
    }
}