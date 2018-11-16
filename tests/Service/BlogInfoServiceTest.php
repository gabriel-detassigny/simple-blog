<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use GabrielDeTassigny\Blog\Entity\BlogInfo;
use GabrielDeTassigny\Blog\Repository\BlogInfoRepository;
use GabrielDeTassigny\Blog\Service\BlogInfoService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class BlogInfoServiceTest extends TestCase
{
    /** @var BlogInfoService */
    private $blogInfoService;

    /** @var BlogInfoRepository|Phake_IMock */
    private $blogInfoRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->blogInfoRepository = Phake::mock(BlogInfoRepository::class);
        $this->blogInfoService = new BlogInfoService($this->blogInfoRepository);
    }

    public function testGetBlogTitle(): void
    {
        $blogInfo = $this->createBlogInfo('blog_title', 'Blog Title');
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => 'blog_title'])->thenReturn($blogInfo);

        $result = $this->blogInfoService->getBlogTitle();

        $this->assertSame($blogInfo->getValue(), $result);
    }

    public function testGetBlogTitle_NotFound(): void
    {
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => 'blog_title'])->thenReturn(null);

        $result = $this->blogInfoService->getBlogTitle();

        $this->assertNull($result);
    }

    public function testGetBlogDescription(): void
    {
        $blogInfo = $this->createBlogInfo('blog_description', 'Blog Description');
        Phake::when($this->blogInfoRepository)->findOneBy(['key' => 'blog_description'])->thenReturn($blogInfo);

        $result = $this->blogInfoService->getBlogDescription();

        $this->assertSame($blogInfo->getValue(), $result);
    }

    private function createBlogInfo(string $key, string $value): BlogInfo
    {
        $blogInfo = new BlogInfo();
        $blogInfo->setKey($key);
        $blogInfo->setValue($value);

        return $blogInfo;
    }
}
