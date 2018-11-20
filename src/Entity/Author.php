<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Entity;

/**
 * @Entity(repositoryClass="GabrielDeTassigny\Blog\Repository\AuthorRepository")
 * @Table(name="authors")
 */
class Author
{
    /** @Id @Column(type="integer") @GeneratedValue */
    private $id;

    /** @Column(type="string", length=50, nullable=false) */
    private $name;

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
}