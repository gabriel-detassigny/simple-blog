<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\Comment;

class CommentService
{
    /** @var EntityManager */
    private $entityManager;

    /** @var PostViewingService */
    private $postViewingService;

    public function __construct(EntityManager $entityManager, PostViewingService $postViewingService)
    {
        $this->entityManager = $entityManager;
        $this->postViewingService = $postViewingService;
    }

    /**
     * @param int $postId
     * @return Collection
     * @throws CommentException
     */
    public function getPostComments(int $postId): Collection
    {
        try {
            $post = $this->postViewingService->getPost($postId);
        } catch (PostNotFoundException $e) {
            throw new CommentException($e->getMessage());
        }

        return $post->getComments();
    }

    /**
     * @param array $request
     * @param int $postId
     * @return Comment
     * @throws CommentException
     */
    public function createComment(array $request, int $postId): Comment
    {
        $comment = new Comment();
        $comment->setText($request['text']);
        $comment->setName($request['name']);
        $comment->setCreatedAt(new DateTime());
        $this->findAndSetPost($comment, $postId);
        $this->validateComment($comment);

        try {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new CommentException($e->getMessage(), CommentException::DB_ERROR, $e);
        }

        return $comment;
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

    private function validateComment(Comment $comment): void
    {
        if (empty($comment->getName())) {
            throw new CommentException('Empty name field', CommentException::FIELD_ERROR);
        }
        if (empty($comment->getText())) {
            throw new CommentException('Empty comment field', CommentException::FIELD_ERROR);
        }
    }
}