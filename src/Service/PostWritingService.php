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
     * @throws PostCreationException
     * @return void
     */
    public function createPost(array $request): void
    {
        $post = new Post();
        $this->setPostFromRequest($post, $request);

        try {
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new PostCreationException('Error on post creation: ' . $e->getMessage());
        }
    }

    /**
     * @param Post $post
     * @param array $request
     * @throws PostUpdatingException
     * @return void
     */
    public function updatePost(Post $post, array $request): void
    {
        $this->setPostFromRequest($post, $request);

        try {
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new PostUpdatingException('Error when updating post: ' . $e->getMessage());
        }
    }

    /**
     * @param Post $post
     * @param array $request
     * @return void
     */
    private function setPostFromRequest(Post $post, array $request): void
    {
        $post->setText($request['text']);
        $post->setSubtitle($request['subtitle']);
        $post->setTitle($request['title']);
        $post->setCreatedAt(new DateTime());
        $this->findAndSetAuthor($post, (int)$request['author']);
    }

    /**
     * @param Post $post
     * @param int $authorId
     * @throws PostCreationException
     * @return void
     */
    private function findAndSetAuthor(Post $post, int $authorId): void
    {
        try {
            $author = $this->authorService->getAuthorById($authorId);
        } catch (AuthorException $e) {
            throw new PostCreationException($e->getMessage());
        }
        $post->setAuthor($author);
    }
}