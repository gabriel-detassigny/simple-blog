<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use GabrielDeTassigny\Blog\Entity\Post;
use GabrielDeTassigny\Blog\ValueObject\Page;

class PostRepository extends EntityRepository
{
    public function searchPageOfLatestPosts(Page $page, int $pageSize)
    {
        $dql = 'SELECT p FROM ' . Post::class . ' p ORDER BY p.createdAt DESC';

        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult($page->getValue() - 1)
            ->setMaxResults($pageSize);

        return new Paginator($query);
    }
}
