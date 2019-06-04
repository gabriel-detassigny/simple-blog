<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\Comment;
use GabrielDeTassigny\Blog\Repository\CommentRepository;
use GabrielDeTassigny\Blog\Service\Exception\CommentException;
use GabrielDeTassigny\Blog\Service\Exception\PostNotFoundException;

class CommentService
{
    /** @var EntityManager */
    private $entityManager;

    /** @var PostViewingService */
    private $postViewingService;

    /** @var CommentRepository */
    private $commentRepository;

    public function __construct(
        EntityManager $entityManager,
        PostViewingService $postViewingService,
        CommentRepository $commentRepository
    ) {
        $this->entityManager = $entityManager;
        $this->postViewingService = $postViewingService;
        $this->commentRepository = $commentRepository;
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
    public function createUserComment(array $request, int $postId): Comment
    {
        $comment = new Comment();
        $comment->setText($request['text']);
        $comment->setName($request['name']);
        $comment->setCreatedAt(new DateTime());
        $this->findAndSetPost($comment, $postId);
        $this->validateComment($comment);

        $this->persistComment($comment);

        return $comment;
    }

    /**
     * @param string $text
     * @param int $postId
     * @return Comment
     * @throws CommentException
     */
    public function createAdminComment(string $text, int $postId): Comment
    {
        $comment = new Comment();
        $comment->setText($text);
        $comment->setAsAdmin();
        $comment->setCreatedAt(new DateTime());
        $this->findAndSetPost($comment, $postId);
        $comment->setName($comment->getPost()->getAuthor()->getName());
        $this->validateComment($comment);

        $this->persistComment($comment);

        return $comment;
    }

    /**
     * @param int $commentId
     * @throws CommentException
     */
    public function deleteComment(int $commentId): void
    {
        $comment = $this->commentRepository->find($commentId);
        if (!$comment) {
            throw new CommentException('Comment not found', CommentException::DB_ERROR);
        }
        try {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new CommentException($e->getMessage(), CommentException::DB_ERROR);
        }
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

    /**
     * @param Comment $comment
     */
    private function persistComment(Comment $comment): void
    {
        try {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new CommentException($e->getMessage(), CommentException::DB_ERROR, $e);
        }
    }
}