<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Repository\AuthorRepository;
use GabrielDeTassigny\Blog\Service\Exception\AuthorException;

class AuthorService
{
    /** @var AuthorRepository */
    private $authorRepository;

    /** @var EntityManager */
    private $entityManager;

    public function __construct(AuthorRepository $authorRepository, EntityManager $entityManager)
    {
        $this->authorRepository = $authorRepository;
        $this->entityManager = $entityManager;
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
     * @throws AuthorException
     */
    public function getAuthorById(int $id): Author
    {
        /** @var Author $author */
        $author = $this->authorRepository->find($id);
        if (!$author) {
            throw new AuthorException("Could not find author with ID {$id}", AuthorException::FIND_ERROR);
        }
        return $author;
    }

    /**
     * @param string $name
     * @return Author
     * @throws AuthorException
     */
    public function createAuthor(string $name): Author
    {
        $author = new Author();
        $author->setName($name);
        try {
            $this->entityManager->persist($author);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new AuthorException('Error in author creation: ' . $e->getMessage(), AuthorException::CREATE_ERROR);
        }
        return $author;
    }
}