<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Entity;

/**
 * @Entity(repositoryClass="GabrielDeTassigny\Blog\Repository\ExternalLinkRepository")
 * @Table(name="external_links")
 */
class ExternalLink
{
    /** @Id @Column(type="integer") @GeneratedValue */
    private $id;

    /** @Column(type="string", length=50, nullable=false) */
    private $name;

    /** @Column(type="string", length=200, nullable=false) */
    private $url;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}