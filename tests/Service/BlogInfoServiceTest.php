<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use Doctrine\ORM\EntityManager;
use GabrielDeTassigny\Blog\Entity\BlogInfo;
use GabrielDeTassigny\Blog\Repository\BlogInfoRepository;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class BlogInfoServiceTest extends TestCase
{
    private const BLOG_TITLE = 'Blog Title';
    private const BLOG_DESCRIPTION = 'Blog Description';
    private const ABOUT_TEXT = 'This blog is written by ... about ...';
    private const BLOG_TITLE_KEY = 'blog_title';
    private const ABOUT_TEXT_KEY = 'about_text';
    private const BLOG_DESCRIPTION_KEY = 'blog_description';

    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var BlogInfoRepository|Phake_IMock */
    private $blogInfoRepository;

    /** @var EntityManager|Phake_IMock */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->blogInfoRepository = Phake::mock(BlogInfoRepository::class);
        $this->entityManager = Phake::mock(EntityManager::class);
        $this->blogInfoService = new BlogInfoService($this->blogInfoRepository, $this->entityManager);
    }

    public function testGetBlogTitle(): void
    {
        $blogInfo = $this->createBlogInfo(self::BLOG_TITLE_KEY, self::BLOG_TITLE);
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => self::BLOG_TITLE_KEY])->thenReturn($blogInfo);

        $result = $this->blogInfoService->getBlogTitle();

        $this->assertSame($blogInfo->getValue(), $result);
    }

    public function testGetBlogTitle_NotFound(): void
    {
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => self::BLOG_TITLE_KEY])->thenReturn(null);

        $result = $this->blogInfoService->getBlogTitle();

        $this->assertNull($result);
    }

    public function testGetBlogDescription(): void
    {
        $blogInfo = $this->createBlogInfo(self::BLOG_DESCRIPTION_KEY, self::BLOG_DESCRIPTION);
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => self::BLOG_DESCRIPTION_KEY])->thenReturn($blogInfo);

        $result = $this->blogInfoService->getBlogDescription();

        $this->assertSame($blogInfo->getValue(), $result);
    }

    public function testGetAboutText(): void
    {
        $blogInfo = $this->createBlogInfo(self::ABOUT_TEXT_KEY, self::ABOUT_TEXT);
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => self::ABOUT_TEXT_KEY])->thenReturn($blogInfo);

        $result = $this->blogInfoService->getAboutText();

        $this->assertSame($blogInfo->getValue(), $result);
    }

    public function testSetBlogTitle_createBlogTitle(): void
    {
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => self::BLOG_TITLE_KEY])->thenReturn(null);

        $this->blogInfoService->setBlogTitle(self::BLOG_TITLE);

        /** @var BlogInfo $blogInfo */
        Phake::verify($this->entityManager)->persist(Phake::capture($blogInfo));
        $this->assertSame(self::BLOG_TITLE_KEY, $blogInfo->getKey());
        $this->assertSame(self::BLOG_TITLE, $blogInfo->getValue());
    }

    public function testSetBlogTitle_Delete(): void
    {
        $blogInfo = $this->createBlogInfo(self::BLOG_TITLE_KEY, self::BLOG_TITLE);
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => self::BLOG_TITLE_KEY])->thenReturn($blogInfo);

        $this->blogInfoService->setBlogTitle(null);

        Phake::verify($this->entityManager)->remove($blogInfo);
    }

    public function testSetBlogTitle_UpdateValue(): void
    {
        $blogInfo = Phake::mock(BlogInfo::class);
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => self::BLOG_TITLE_KEY])->thenReturn($blogInfo);

        $this->blogInfoService->setBlogTitle(self::BLOG_TITLE);

        Phake::inOrder(
            Phake::verify($blogInfo)->setValue(self::BLOG_TITLE),
            Phake::verify($this->entityManager)->persist($blogInfo)
        );
    }

    public function testSetBlogDescription(): void
    {
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => self::BLOG_DESCRIPTION_KEY])->thenReturn(null);

        $this->blogInfoService->setBlogDescription(self::BLOG_DESCRIPTION);

        /** @var BlogInfo $blogInfo */
        Phake::verify($this->entityManager)->persist(Phake::capture($blogInfo));
        $this->assertSame(self::BLOG_DESCRIPTION_KEY, $blogInfo->getKey());
        $this->assertSame(self::BLOG_DESCRIPTION, $blogInfo->getValue());
    }

    public function testSetAboutText(): void
    {
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => self::ABOUT_TEXT_KEY])->thenReturn(null);

        $this->blogInfoService->setAboutText(self::ABOUT_TEXT);

        /** @var BlogInfo $blogInfo */
        Phake::verify($this->entityManager)->persist(Phake::capture($blogInfo));
        $this->assertSame(self::ABOUT_TEXT_KEY, $blogInfo->getKey());
        $this->assertSame(self::ABOUT_TEXT, $blogInfo->getValue());
    }

    private function createBlogInfo(string $key, string $value): BlogInfo
    {
        $blogInfo = new BlogInfo();
        $blogInfo->setKey($key);
        $blogInfo->setValue($value);

        return $blogInfo;
    }
}
