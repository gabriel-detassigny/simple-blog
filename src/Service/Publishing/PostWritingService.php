<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Publishing;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Service\Exception\AuthorException;
use GabrielDeTassigny\Blog\Service\AuthorService;
use GabrielDeTassigny\Blog\Service\Exception\PostWritingException;
use GabrielDeTassigny\Blog\ValueObject\CommentType;
use GabrielDeTassigny\Blog\ValueObject\InvalidCommentTypeException;
use GabrielDeTassigny\Blog\ValueObject\InvalidStateException;
use GabrielDeTassigny\Blog\ValueObject\PostState;

class PostWritingService
{
    /** @var EntityManager */
    private $entityManager;

    /** @var AuthorService */
    private $authorService;

    public function __construct(EntityManager $entityManager, AuthorService $authorService)
    {
        $this->entityManager = $entityManager;
        $this->authorService = $authorService;
    }

    /**
     * @throws PostWritingException
     */
    public function createPost(array $request): Post
    {
        $post = new Post();
        $this->setPostFromRequest($post, $request);
        $post->setCreatedAt(new DateTime());

        try {
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new PostWritingException(
                'Error on post creation: ' . $e->getMessage(),
                PostWritingException::DB_ERROR
            );
        }
        return $post;
    }

    /**
     * @throws PostWritingException
     */
    public function updatePost(Post $post, array $request): void
    {
        $this->setPostFromRequest($post, $request);
        $post->setUpdatedAt(new DateTime());
        try {
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new PostWritingException(
                'Error when updating post: ' . $e->getMessage(),
                PostWritingException::DB_ERROR
            );
        }
    }

    /**
     * @throws PostWritingException
     */
    private function setPostFromRequest(Post $post, array $request): void
    {
        $post->setText($request['text']);
        $post->setSubtitle($request['subtitle']);
        $post->setTitle($request['title']);

        $this->ensurePostIsValid($post);

        $this->findAndSetAuthor($post, (int)$request['author']);
        $this->setPostState($post, $request['state'] ?? '');
        $this->setCommentType($post, $request['comment-type']);
    }

    /**
     * @throws PostWritingException
     */
    private function ensurePostIsValid(Post $post): void
    {
        if (empty($post->getTitle())) {
            throw new PostWritingException('Title should not be empty', PostWritingException::TITLE_ERROR);
        }
        if (empty($post->getText())) {
            throw new PostWritingException('Text should not be empty', PostWritingException::TEXT_ERROR);
        }
    }

    /**
     * @throws PostWritingException
     */
    private function findAndSetAuthor(Post $post, int $authorId): void
    {
        try {
            $author = $this->authorService->getAuthorById($authorId);
        } catch (AuthorException $e) {
            throw new PostWritingException($e->getMessage(), PostWritingException::AUTHOR_ERROR);
        }
        $post->setAuthor($author);
    }

    /**
     * @throws PostWritingException
     */
    private function setPostState(Post $post, string $stateValue): void
    {
        try {
            $post->setState(new PostState($stateValue));
        } catch (InvalidStateException $e) {
            throw new PostWritingException($e->getMessage(), PostWritingException::STATE_ERROR);
        }
    }

    /**
     * @throws PostWritingException
     */
    private function setCommentType(Post $post, string $commentTypeValue): void
    {
        try {
            $post->setCommentType(new CommentType($commentTypeValue));
        } catch (InvalidCommentTypeException $e) {
            throw new PostWritingException($e->getMessage(), PostWritingException::COMMENT_TYPE_ERROR);
        }
    }
}