<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller\Admin;

use Exception;
use GabrielDeTassigny\Blog\Renderer\ErrorRenderer;
use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\Exception\ExternalLinkException;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig\Environment;

class ExternalLinkController extends AbstractAdminController
{
    private const SUCCESS_MESSAGE = 'External Link was successfully created!';
    private const MANDATORY_FIELDS = ['name', 'url', 'author'];

    /** @var AdminAuthenticator */
    private $authenticationService;

    /** @var Environment */
    private $twig;

    /** @var ExternalLinkService */
    private $externalLinkService;

    /** @var ServerRequestInterface */
    private $request;

    /** @var ErrorRenderer */
    private $errorRenderer;

    /** @var AuthorService */
    private $authorService;

    public function __construct(
        AdminAuthenticator $authenticationService,
        Environment $twig,
        ExternalLinkService $externalLinkService,
        ServerRequestInterface $request,
        ErrorRenderer $errorRenderer,
        AuthorService $authorService
    ) {
        $this->authenticationService = $authenticationService;
        $this->twig = $twig;
        $this->externalLinkService = $externalLinkService;
        $this->request = $request;
        $this->errorRenderer = $errorRenderer;
        $this->authorService = $authorService;
    }

    public function newExternalLink(array $vars): void
    {
        $this->ensureAdminAuthentication();

        $this->twig->display('external-links/new.twig', ['author' => (int) $vars['authorId']]);
    }

    public function createExternalLink(): void
    {
        $this->ensureAdminAuthentication();
        $formParams = $this->ensureValidFormParams();

        $authorId = (int) $formParams['author'];

        try {
            $link = $this->externalLinkService->createExternalLink($formParams['name'], $formParams['url']);
            $this->authorService->addExternalLink($authorId, $link);

            $this->twig->display('external-links/new.twig', [
                'success' => self::SUCCESS_MESSAGE,
                'author' => $authorId
            ]);
        } catch (ExternalLinkException $e) {
            $this->twig->display('external-links/new.twig', [
                'error' => $e->getMessage(),
                'author' => $authorId
            ]);
        }
    }

    public function deleteExternalLink(array $vars): void
    {
        $this->ensureAdminAuthentication();

        try {
            $externalLink = $this->externalLinkService->getExternalLink((int) $vars['id']);
            $this->authorService->removeExternalLink((int) $vars['authorId'], $externalLink);
        } catch (Exception $e) {
            $this->errorRenderer->setContentTypeToJson();
            $this->errorRenderer->renderError(StatusCode::BAD_REQUEST, $e->getMessage());
        }
    }

    protected function getAdminAuthenticator(): AdminAuthenticator
    {
        return $this->authenticationService;
    }

    private function ensureValidFormParams(): array
    {
        $body = $this->request->getParsedBody();

        if (!isset($body['link']) || !is_array($body['link'])) {
            throw new HttpException('Invalid form parameters', StatusCode::BAD_REQUEST);
        }

        foreach (self::MANDATORY_FIELDS as $mandatoryField) {
            if (!isset($body['link'][$mandatoryField])) {
                throw new HttpException('Missing data ' . $mandatoryField, StatusCode::BAD_REQUEST);
            }
        }

        return $body['link'];
    }
}