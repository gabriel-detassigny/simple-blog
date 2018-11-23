<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Repository\AuthorRepository;

class AuthorService
{
    /** @var AuthorRepository */
    private $authorRepository;

    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    /**
     * @return Author[]
     */
    public function getAuthors(): array
    {
        return $this->authorRepository->findAll();
    }

    /**
     * @param int $id
     * @return Author
     * @throws AuthorNotFoundException
     */
    public function getAuthorById(int $id): Author
    {
        /** @var Author $author */
        $author = $this->authorRepository->find($id);
        if (!$author) {
            throw new AuthorNotFoundException("Could not find author with ID {$id}");
        }
        return $author;
    }
}