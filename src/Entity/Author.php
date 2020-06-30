<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Entity;

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