<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\Post;

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
     * @param array $request
     * @throws PostWritingException
     * @return Post
     */
    public function createPost(array $request): Post
    {
        $post = new Post();
        $this->setPostFromRequest($post, $request);

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
     * @param Post $post
     * @param array $request
     * @throws PostWritingException
     * @return void
     */
    public function updatePost(Post $post, array $request): void
    {
        $this->setPostFromRequest($post, $request);

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
     * @param Post $post
     * @param array $request
     * @return void
     * @throws PostWritingException
     */
    private function setPostFromRequest(Post $post, array $request): void
    {
        $post->setText($request['text']);
        $post->setSubtitle($request['subtitle']);
        $post->setTitle($request['title']);
        $post->setCreatedAt(new DateTime());
        $this->ensurePostIsValid($post);
        $this->findAndSetAuthor($post, (int)$request['author']);
    }

    /**
     * @param Post $post
     * @return void
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
     * @param Post $post
     * @param int $authorId
     * @throws PostWritingException
     * @return void
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
}