<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use GabrielDeTassigny\Blog\Entity\BlogInfo;
use GabrielDeTassigny\Blog\Repository\BlogInfoRepository;

class BlogInfoService
{
    /** @var BlogInfoRepository */
    private $blogInfoRepository;

    const BLOG_TITLE = 'blog_title';
    const BLOG_DESCRIPTION = 'blog_description';

    public function __construct(BlogInfoRepository $blogInfoRepository)
    {
        $this->blogInfoRepository = $blogInfoRepository;
    }

    public function getBlogTitle(): ?string
    {
        return $this->getBlogInfoValueFromKey(self::BLOG_TITLE);
    }

    public function getBlogDescription(): ?string
    {
        return $this->getBlogInfoValueFromKey(self::BLOG_DESCRIPTION);
    }

    private function getBlogInfoValueFromKey(string $key): ?string
    {
        /** @var BlogInfo $blogInfo */
        $blogInfo = $this->blogInfoRepository->findOneBy(['key' => $key]);
        if (!$blogInfo) {
            return null;
        }
        return $blogInfo->getValue();
    }
}