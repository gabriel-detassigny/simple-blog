<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Repository\PostRepository;
use GabrielDeTassigny\Blog\ValueObject\InvalidPageException;
use GabrielDeTassigny\Blog\ValueObject\Page;

class PostViewingService
{
    const PAGE_SIZE = 10;

    /** @var PostRepository */
    private $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findPageOfLatestPosts(Page $page): Paginator
    {
        return $this->repository->searchPageOfLatestPosts($page, self::PAGE_SIZE);
    }

    public function getPreviousPage(Page $currentPage): ?Page
    {
        try {
            return new Page($currentPage->getValue() - 1);
        } catch (InvalidPageException $e) {
            return null;
        }
    }

    public function getNextPage(Page $currentPage, int $totalPosts): ?Page
    {
        if ($totalPosts <= $currentPage->getValue() * self::PAGE_SIZE) {
            return null;
        }
        return new Page($currentPage->getValue() + 1);
    }
}