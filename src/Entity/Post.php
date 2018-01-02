<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Entity;

/**
 * @Entity
 * @Table(
 *     name="posts",
 *     indexes={@Index(name="title", columns={"title"})}
 * )
 */
class Post
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
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
}
