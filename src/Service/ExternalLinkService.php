<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use GabrielDeTassigny\Blog\Entity\ExternalLink;
use GabrielDeTassigny\Blog\Repository\ExternalLinkRepository;

class ExternalLinkService
{
    /** @var ExternalLinkRepository */
    private $externalLinkRepository;

    /**
     * @param ExternalLinkRepository $externalLinkRepository
     */
    public function __construct(ExternalLinkRepository $externalLinkRepository)
    {
        $this->externalLinkRepository = $externalLinkRepository;
    }

    /**
     * @return ExternalLink[]
     */
    public function getExternalLinks(): array
    {
        return $this->externalLinkRepository->findAll();
    }
}