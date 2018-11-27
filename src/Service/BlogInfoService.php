<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GabrielDeTassigny\Blog\Entity\BlogInfo;
use GabrielDeTassigny\Blog\Repository\BlogInfoRepository;

class BlogInfoService
{
    /** @var BlogInfoRepository */
    private $blogInfoRepository;

    /** @var EntityManager */
    private $entityManager;

    private const BLOG_TITLE = 'blog_title';
    private const BLOG_DESCRIPTION = 'blog_description';
    private const ABOUT_TEXT = 'about_text';

    public function __construct(BlogInfoRepository $blogInfoRepository, EntityManager $entityManager)
    {
        $this->blogInfoRepository = $blogInfoRepository;
        $this->entityManager = $entityManager;
    }

    public function getBlogTitle(): ?string
    {
        return $this->getBlogInfoValueFromKey(self::BLOG_TITLE);
    }

    public function getBlogDescription(): ?string
    {
        return $this->getBlogInfoValueFromKey(self::BLOG_DESCRIPTION);
    }

    public function getAboutText(): ?string
    {
        return $this->getBlogInfoValueFromKey(self::ABOUT_TEXT);
    }

    public function setBlogTitle(string $blogTitle = null): void
    {
        $this->setBlogInfoValue(self::BLOG_TITLE, $blogTitle);
    }

    public function setBlogDescription(string $blogDescription = null): void
    {
        $this->setBlogInfoValue(self::BLOG_DESCRIPTION, $blogDescription);
    }

    public function setAboutText(string $aboutText = null): void
    {
        $this->setBlogInfoValue(self::ABOUT_TEXT, $aboutText);
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

    private function setBlogInfoValue(string $key, string $value = null)
    {
        /** @var BlogInfo $blogInfo */
        $blogInfo = $this->blogInfoRepository->findOneBy(['key' => $key]);
        if (!$blogInfo) {
            if (!empty($value)) {
                $this->createBlogInfo($key, $value);
            }
        } else {
            if (empty($value)) {
                $this->entityManager->remove($blogInfo);
            } else {
                $blogInfo->setValue($value);
                $this->entityManager->persist($blogInfo);
            }
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     * @throws ORMException
     */
    private function createBlogInfo(string $key, string $value): void
    {
        $blogInfo = new BlogInfo();
        $blogInfo->setKey($key);
        $blogInfo->setValue($value);
        $this->entityManager->persist($blogInfo);
    }
}