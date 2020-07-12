<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\ExternalLink;
use GabrielDeTassigny\Blog\Repository\ExternalLinkRepository;
use GabrielDeTassigny\Blog\Service\Exception\ExternalLinkException;

class ExternalLinkService
{
    /** @var ExternalLinkRepository */
    private $externalLinkRepository;

    /** @var EntityManager */
    private $entityManager;

    public function __construct(ExternalLinkRepository $externalLinkRepository, EntityManager $entityManager)
    {
        $this->externalLinkRepository = $externalLinkRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @return ExternalLink[]
     */
    public function getExternalLinks(): array
    {
        return $this->externalLinkRepository->findAll();
    }

    public function getExternalLink(int $id): ExternalLink
    {
        /** @var ExternalLink $externalLink */
        $externalLink = $this->externalLinkRepository->find($id);

        if (!$externalLink) {
            throw new ExternalLinkException(
                'Could not find external link with ID ' . $id,
                ExternalLinkException::FIND_ERROR
            );
        }

        return $externalLink;
    }

    /**
     * @param string $name
     * @param string $url
     * @return ExternalLink
     * @throws ExternalLinkException
     */
    public function createExternalLink(string $name, string $url): ExternalLink
    {
        $externalLink = new ExternalLink();
        $externalLink->setName($name);
        $externalLink->setUrl($url);

        try {
            $this->entityManager->persist($externalLink);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new ExternalLinkException(
                'Error creating external link: ' . $e->getMessage(),
                ExternalLinkException::CREATE_ERROR
            );
        }

        return $externalLink;
    }
}