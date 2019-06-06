<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Controller\Admin;

use GabrielDeTassigny\Blog\Controller\Admin\AbstractAdminController;
use GabrielDeTassigny\Blog\Service\Authentication\AdminAuthenticator;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\HttpException;
use Teapot\StatusCode;
use Twig_Environment;
use Twig_Error;

class BlogInfoController extends AbstractAdminController
{
    private const SUCCESS_MESSAGE = 'Blog Configuration successfully updated!';

    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var Twig_Environment */
    private $twig;

    /** @var AdminAuthenticator */
    private $authenticationService;

    /** @var ServerRequestInterface */
    private $request;

    public function __construct(
        Twig_Environment $twig,
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
     * @throws Twig_Error
     */
    public function edit()
    {
        $this->ensureAdminAuthentication();
        $blogTitle = $this->blogInfoService->getBlogTitle();
        $blogDescription = $this->blogInfoService->getBlogDescription();
        $aboutText = $this->blogInfoService->getAboutText();

        $this->twig->display(
            'blog-info/edit.twig',
            ['title' => $blogTitle, 'description' => $blogDescription, 'about' => $aboutText]
        );
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
        $this->twig->display(
            'blog-info/edit.twig',
            [
                'title' => $blog['title'],
                'description' => $blog['description'],
                'about' => $blog['about'],
                'success' => self::SUCCESS_MESSAGE
            ]
        );
    }

    protected function getAdminAuthenticator(): AdminAuthenticator
    {
        return $this->authenticationService;
    }
}