<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller;

use GabrielDeTassigny\Blog\Renderer\JsonRenderer;
use GabrielDeTassigny\Blog\Service\AuthenticationService;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use Psr\Http\Message\ServerRequestInterface;
use Twig_Environment;

class ExternalLinkController extends AdminController
{
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

    protected function getAuthenticationService(): AuthenticationService
    {
        return $this->authenticationService;
    }
}