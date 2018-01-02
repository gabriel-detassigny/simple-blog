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

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
