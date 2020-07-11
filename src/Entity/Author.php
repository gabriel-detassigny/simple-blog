<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="GabrielDeTassigny\Blog\Repository\AuthorRepository")
 * @ORM\Table(name="authors")
 */
class Author
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
    private $id;

    /** @ORM\Column(type="string", length=50, nullable=false) */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=ExternalLink::class, orphanRemoval=true)
     * @ORM\JoinTable(
     *     name="author_external_links",
     *     joinColumns={@ORM\JoinColumn(name="author_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="external_link_id", unique=true)}
     * )
     * @var Collection
     */
    private $externalLinks;

    public function __construct()
    {
        $this->externalLinks = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getExternalLinks(): Collection
    {
        return $this->externalLinks;
    }

    public function addExternalLink(ExternalLink $externalLink): void
    {
        $this->externalLinks->add($externalLink);
    }

    public function removeExternalLink(ExternalLink $externalLink): void
    {
        $this->externalLinks->removeElement($externalLink);
    }
}