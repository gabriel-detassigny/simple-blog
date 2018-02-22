<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Entity;

use DateTime;

/**
 * @Entity(repositoryClass="GabrielDeTassigny\Blog\Repository\PostRepository")
 * @Table(
 *     name="posts",
 *     indexes={@Index(name="title", columns={"title"})}
 * )
 */
class Post
{
    /** @Id @Column(type="integer") @GeneratedValue */
    private $id;

    /** @Column(type="text", length=20000, nullable=false) */
    private $text;

    /** @Column(type="string", length=50, nullable=false) */
    private $title;

    /** @Column(type="string", length=150, nullable=false) */
    private $subtitle;

    /** @Column(type="datetime", name="created_at", nullable=false) */
    private $createdAt;

    /** @Column(type="datetime", name="updated_at", nullable=true) */
    private $updatedAt;

    public function getText(): string
    {
        return $this->text;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
