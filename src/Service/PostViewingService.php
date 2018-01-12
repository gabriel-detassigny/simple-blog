<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Repository\PostRepository;
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

    public function findPageOfLatestPosts(): Paginator
    {
        return $this->repository->searchPageOfLatestPosts(new Page(1), self::PAGE_SIZE);
    }
}