<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use GabrielDeTassigny\Blog\Entity\ExternalLink;
use GabrielDeTassigny\Blog\Repository\ExternalLinkRepository;
use GabrielDeTassigny\Blog\Service\ExternalLinkService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class ExternalLinkServiceTest extends TestCase
{
    /** @var ExternalLinkRepository|Phake_IMock */
    private $externalLinkRepository;

    /** @var ExternalLinkService */
    private $externalLinkService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->externalLinkRepository = Phake::mock(ExternalLinkRepository::class);
        $this->externalLinkService = new ExternalLinkService($this->externalLinkRepository);
    }

    public function testGetExternalLinks(): void
    {
        $entities = $this->getExternalLinksEntities();
        Phake::when($this->externalLinkRepository)->findAll()->thenReturn($entities);

        $result = $this->externalLinkService->getExternalLinks();

        $this->assertSame($entities, $result);
    }

    private function getExternalLinksEntities(): array
    {
        $values = [
            'Github' => 'https://github.com/some-user',
            'LinkedIn' => 'https://linkedin.com/some-user'
        ];

        $entities = array();
        foreach ($values as $name => $url) {
            $entity = new ExternalLink();
            $entity->setName($name);
            $entity->setUrl($url);

            $entities[] = $entity;
        }

        return $entities;
    }
}
