<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="GabrielDeTassigny\Blog\Repository\CommentRepository")
 * @ORM\Table(
 *     name="comments",
 *     indexes={
 *         @ORM\Index(name="post_id", columns={"post_id"})
 *     }
 * )
 */
class Comment
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
    private $id;

    /** @ORM\Column(type="string", length=50, nullable=false) */
    private $name;

    /** @ORM\Column(type="text", length=500, nullable=false) */
    private $text;

    /** @ORM\Column(type="boolean", nullable=false) */
    private $isAdmin = false;

    /** @ORM\Column(type="datetime", name="created_at", nullable=false) */
    private $createdAt;

    /** @ORM\ManyToOne(targetEntity=Post::class, inversedBy="comments") */
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