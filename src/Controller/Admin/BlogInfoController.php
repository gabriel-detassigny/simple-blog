<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller\Admin;

use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig\Environment;
use Twig\Error\Error;

class BlogInfoController extends AbstractAdminController
{
    private const SUCCESS_MESSAGE = 'Blog Configuration successfully updated!';

    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var Environment */
    private $twig;

    /** @var AdminAuthenticator */
    private $authenticationService;

    /** @var ServerRequestInterface */
    private $request;

    public function __construct(
        Environment $twig,
        BlogInfoService $blogInfoService,
        AdminAuthenticator $authenticationService,
        ServerRequestInterface $request
    ) {
        $this->twig = $twig;
        $this->blogInfoService = $blogInfoService;
        $this->authenticationService = $authenticationService;
        $this->request = $request;
    }

    /**
     * @throws HttpException
     * @throws Error
     */
    public function edit()
    {
        $this->ensureAdminAuthentication();

        $this->twig->display('blog-info/edit.twig', [
            'title' => $this->blogInfoService->getBlogTitle(),
            'description' => $this->blogInfoService->getBlogDescription(),
            'about' => $this->blogInfoService->getAboutText()
        ]);
    }

    public function update()
    {
        $this->ensureAdminAuthentication();

        $body = $this->request->getParsedBody();
        if (!is_array($body) || !array_key_exists('blog', $body) || !is_array($body['blog'])) {
            throw new HttpException('Invalid form parameters', StatusCode::BAD_REQUEST);
        }

        $blog = $body['blog'];

        $this->blogInfoService->setBlogTitle($blog['title']);
        $this->blogInfoService->setBlogDescription($blog['description']);
        $this->blogInfoService->setAboutText($blog['about']);

        $this->twig->display('blog-info/edit.twig', [
            'title' => $blog['title'],
            'description' => $blog['description'],
            'about' => $blog['about'],
            'success' => self::SUCCESS_MESSAGE
        ]);
    }

    protected function getAdminAuthenticator(): AdminAuthenticator
    {
        return $this->authenticationService;
    }
}