<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
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
     */
    public function createPost(array $request): void
    {
        $post = new Post();
        $post->setText($request['text']);
        $post->setSubtitle($request['subtitle']);
        $post->setTitle($request['title']);
        $post->setCreatedAt(new DateTime());
        $this->findAndSetAuthor($post, (int)$request['author']);

        try {
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new PostCreationException('Error on post creation : ' . $e->getMessage());
        }
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