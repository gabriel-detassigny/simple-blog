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
    private const UNDEFINED_SLUG = 'n-a';

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

    /** @ManyToOne(targetEntity="GabrielDeTassigny\Blog\Entity\Author") */
    private $author;

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getSubtitle(): ?string
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

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): void
    {
        $this->author = $author;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getSlug(): string
    {
        $slug = trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->getTitle()), '-');
        $slug = strtolower(preg_replace('/-+/', '-', $slug));

        if (empty($slug)) {
            return self::UNDEFINED_SLUG;
        }

        return $slug;
    }
}
