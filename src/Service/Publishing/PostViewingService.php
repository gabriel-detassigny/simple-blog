<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Publishing;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\Service\PostNotFoundException;
use GabrielDeTassigny\Blog\ValueObject\Page;
use GabrielDeTassigny\Blog\ValueObject\PostState;

class PostViewingService
{
    private const DEFAULT_LIMIT = 100;

    /** @var PostRepository */
    private $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findLatestPublishedPosts(int $limit = self::DEFAULT_LIMIT): Paginator
    {
        return $this->findLatestPostsByState($limit, new PostState(PostState::PUBLISHED));
    }

    public function findLatestDraftPosts(int $limit = self::DEFAULT_LIMIT): Paginator
    {
        return $this->findLatestPostsByState($limit, new PostState(PostState::DRAFT));
    }

    /**
     * @param int $id
     * @return Post
     * @throws PostNotFoundException
     */
    public function getPost(int $id): Post
    {
        /** @var Post $post */
        $post = $this->repository->find($id);

        if ($post === null) {
            throw new PostNotFoundException("Post with ID {$id} was not found");
        }
        return $post;
    }

    private function findLatestPostsByState(int $limit, PostState $postState): Paginator
    {
        return $this->repository->searchPageOfLatestPosts(new Page(1), $limit, $postState);
    }
}