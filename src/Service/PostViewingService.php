<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\ValueObject\InvalidPageException;
use GabrielDeTassigny\Blog\ValueObject\Page;

class PostViewingService
{
    private const DEFAULT_PAGE_SIZE = 10;

    /** @var PostRepository */
    private $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findPageOfLatestPosts(Page $page, int $pageSize = self::DEFAULT_PAGE_SIZE): Paginator
    {
        return $this->repository->searchPageOfLatestPosts($page, $pageSize);
    }

    public function getPreviousPage(Page $currentPage): ?Page
    {
        try {
            return new Page($currentPage->getValue() - 1);
        } catch (InvalidPageException $e) {
            return null;
        }
    }

    public function getNextPage(Page $currentPage, int $totalPosts, int $pageSize = self::DEFAULT_PAGE_SIZE): ?Page
    {
        if ($totalPosts <= $currentPage->getValue() * $pageSize) {
            return null;
        }
        return new Page($currentPage->getValue() + 1);
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
}