<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use GabrielDeTassigny\Blog\Repository\PostRepository;

class PostViewingService
{
    /** @var PostRepository */
    private $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findPageOfLatestPosts()
    {
        return $this->repository->findAll();
    }
}