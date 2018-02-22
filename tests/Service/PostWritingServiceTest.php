<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use Doctrine\ORM\EntityManager;
use Exception;
use GabrielDeTassigny\Blog\Service\PostCreationException;
use GabrielDeTassigny\Blog\Service\PostWritingService;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;

class PostWritingServiceTest extends TestCase
{
    /** @var PostWritingService */
    private $service;

    /** @var EntityManager|Phake_IMock */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->entityManager = Phake::mock(EntityManager::class);
        $this->service = new PostWritingService($this->entityManager);
    }

    public function testCreatePost()
    {
        $request = ['text' => 'example of text', 'title' => 'Title', 'subtitle' => 'Subtitle'];

        $this->service->createPost($request);

        Phake::inOrder(
            Phake::verify($this->entityManager)->persist(Phake::anyParameters()),
            Phake::verify($this->entityManager)->flush()
        );
    }

    public function testCreatePost_DoctrineException()
    {
        $this->expectException(PostCreationException::class);

        Phake::when($this->entityManager)->persist(Phake::anyParameters())->thenThrow(new Exception());
        $request = ['text' => 'example of text', 'title' => 'Title', 'subtitle' => 'Subtitle'];

        $this->service->createPost($request);
    }
}
