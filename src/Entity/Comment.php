<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Entity;

use DateTime;

/**
 * @Entity(repositoryClass="GabrielDeTassigny\Blog\Repository\CommentRepository")
 * @Table(name="comments")
 */
class Comment
{
    /** @Id @Column(type="integer") @GeneratedValue */
    private $id;

    /** @Column(type="string", length=50, nullable=false) */
    private $name;

    /** @Column(type="text", length=500, nullable=false) */
    private $text;

    /** @Column(type="boolean", nullable=false) */
    private $isAdmin = false;

    /** @Column(type="datetime", name="created_at", nullable=false) */
    private $createdAt;

    /** @ManyToOne(targetEntity="GabrielDeTassigny\Blog\Entity\Post") */
    private $post;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    public function IsAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setAsAdmin(): void
    {
        $this->isAdmin = true;
    }
}