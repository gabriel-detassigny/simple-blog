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

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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

        try {
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new PostCreationException('Error on post creation : ' . $e->getMessage());
        }
    }
}