<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\Author;
use GabrielDeTassigny\Blog\Entity\ExternalLink;
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
     * @throws AuthorException
     */
    public function createAuthor(string $name): Author
    {
        $author = new Author();
        $author->setName($name);

        try {
            $this->persistAuthorChanges($author);
        } catch (ORMException $e) {
            throw new AuthorException('Error in author creation: ' . $e->getMessage(), AuthorException::CREATE_ERROR);
        }

        return $author;
    }

    /**
     * @throws AuthorException
     */
    public function addExternalLink(int $authorId, ExternalLink $externalLink): void
    {
        $author = $this->getAuthorById($authorId);
        $author->addExternalLink($externalLink);

        try {
            $this->persistAuthorChanges($author);
        } catch (ORMException $e) {
            throw new AuthorException(
                'Error when adding link: ' . $e->getMessage(),
                AuthorException::LINK_ASSOCIATION_ERROR
            );
        }
    }

    /**
     * @throws AuthorException
     */
    public function removeExternalLink(int $authorId, ExternalLink $externalLink): void
    {
        $author = $this->getAuthorById($authorId);
        $author->removeExternalLink($externalLink);

        try {
            $this->persistAuthorChanges($author);
        } catch (ORMException $e) {
            throw new AuthorException(
                'Error when removing link: ' . $e->getMessage(),
                AuthorException::LINK_ASSOCIATION_ERROR
            );
        }
    }

    /**
     * @throws ORMException
     */
    private function persistAuthorChanges(Author $author): void
    {
        $this->entityManager->persist($author);
        $this->entityManager->flush();
    }
}