<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\ExternalLinkException;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;

class ExternalLinkController extends AdminController
{
    private const SUCCESS_MESSAGE = 'External Link was successfully created!';

    /** @var AuthenticationService */
    private $authenticationService;

    /** @var Twig_Environment */
    private $twig;

    /** @var JsonRenderer */
    private $renderer;

    /** @var ExternalLinkService */
    private $externalLinkService;

    /** @var ServerRequestInterface */
    private $request;

    public function __construct(
        AuthenticationService $authenticationService,
        Twig_Environment $twig,
        JsonRenderer $renderer,
        ExternalLinkService $externalLinkService,
        ServerRequestInterface $request
    ) {
        $this->authenticationService = $authenticationService;
        $this->twig = $twig;
        $this->renderer = $renderer;
        $this->externalLinkService = $externalLinkService;
        $this->request = $request;
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

    protected function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService;
    }
}