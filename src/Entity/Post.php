<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use GabrielDeTassigny\Blog\ValueObject\PostState;

/**
 * @Entity(repositoryClass="GabrielDeTassigny\Blog\Repository\PostRepository")
 * @Table(
 *     name="posts",
 *     indexes={
 *         @Index(name="title", columns={"title"}),
 *         @Index(name="author_id", columns={"author_id"})
 *     }
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

    /** @ManyToOne(targetEntity=Author::class) */
    private $author;

    /** @Column(type="string", options={"default":"published"}, nullable=false) */
    private $state;

    /**
     * @OneToMany(targetEntity=Comment::class, mappedBy="post")
     * @OrderBy({"createdAt": "DESC"})
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

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

    public function getUrl(): string
    {
        return '/posts/' . $this->getId() . '/' . $this->getSlug();
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function isPublished(): bool
    {
        return $this->state === PostState::PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->state === PostState::DRAFT;
    }

    public function setState(PostState $postState): void
    {
        $this->state = $postState->getValue();
    }
}
