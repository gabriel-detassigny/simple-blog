<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="GabrielDeTassigny\Blog\Repository\BlogInfoRepository")
 * @ORM\Table(
 *     name="blog_infos",
 *     indexes={@ORM\Index(name="info_key", columns={"info_key"})}
 * )
 */
class BlogInfo
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
    private $id;

    /** @ORM\Column(name="info_key", type="string", length=20, nullable=false) */
    private $key;

    /** @ORM\Column(name="info_value", type="string", length=200, nullable=false) */
    private $value;

    public function getId(): int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}