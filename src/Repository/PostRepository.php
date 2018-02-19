<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\ValueObject\Page;

class PostRepository extends EntityRepository
{
    private const SQL_PAGE_INDEX = 1;

    public function searchPageOfLatestPosts(Page $page, int $pageSize): Paginator
    {
        $dql = 'SELECT p FROM ' . Post::class . ' p ORDER BY p.createdAt DESC';

        $offset = ($page->getValue() - self::SQL_PAGE_INDEX) * $pageSize;
        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult($offset)
            ->setMaxResults($pageSize);

        return new Paginator($query);
    }
}
