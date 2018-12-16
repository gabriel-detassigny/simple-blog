<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use DateTime;
use Doctrine\ORM\EntityManager;
use GabrielDeTassigny\Blog\Entity\Comment;
use Gregwar\Captcha\CaptchaBuilder;

class CommentService
{
    private const CAPTCHA_KEY = 'captcha';

    /** @var CaptchaBuilder */
    private $captchaBuilder;

    /** @var array */
    private $session;

    /** @var EntityManager */
    private $entityManager;

    /** @var PostViewingService */
    private $postViewingService;

    public function __construct(
        CaptchaBuilder $captchaBuilder,
        array $session,
        EntityManager $entityManager,
        PostViewingService $postViewingService
    ) {
        $this->captchaBuilder = $captchaBuilder;
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->postViewingService = $postViewingService;
    }

    public function getCaptchaImage(): string
    {
        $this->captchaBuilder->build();
        $this->session[self::CAPTCHA_KEY] = $this->captchaBuilder->getPhrase();

        return $this->captchaBuilder->inline();
    }

    public function createComment(array $request, int $postId): void
    {
//        if ($request['captcha'] !== $this->session[self::CAPTCHA_KEY]) {
//            throw new CommentException('Invalid Captcha', CommentException::CAPTCHA_ERROR);
//        }
        $comment = new Comment();
        $comment->setText($request['text']);
        $comment->setName($request['name']);
        $comment->setCreatedAt(new DateTime());
        $this->findAndSetPost($comment, $postId);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    private function findAndSetPost(Comment $comment, int $postId): void
    {
        try {
            $post = $this->postViewingService->getPost($postId);
        } catch (PostNotFoundException $e) {
            throw new CommentException($e->getMessage(), CommentException::FIELD_ERROR, $e);
        }
        $comment->setPost($post);
    }
}