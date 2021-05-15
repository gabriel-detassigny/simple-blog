<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use DateTime;
use GabrielDeTassigny\Blog\Service\ImageService;
use GabrielDeTassigny\Blog\Service\Storage\StorageFactory;
use GabrielDeTassigny\Blog\Service\Storage\StorageInterface;
use Phake;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;

class ImageServiceTest extends TestCase
{
    private const DEFAULT_FILE_PATH = '/tmp/testimage.png';
    private const NEW_FILE_PATH = '/public/testimage.png';

    /** @var LoggerInterface */
    private $logger;

    /** @var ImageService */
    private $service;

    /** @var StorageInterface */
    private $storage;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->logger = Phake::mock(LoggerInterface::class);

        $storageFactory = Phake::mock(StorageFactory::class);
        $this->storage = Phake::mock(StorageInterface::class);
        Phake::when($storageFactory)->create()->thenReturn($this->storage);

        $this->service = new ImageService($this->logger, $storageFactory);
    }

    public function testUploadImage(): void
    {
        $uploadedFile = Phake::mock(UploadedFileInterface::class);
        Phake::when($uploadedFile)->getClientFilename()->thenReturn(self::DEFAULT_FILE_PATH);

        Phake::when($this->storage)->storeFile($uploadedFile)->thenReturn(self::NEW_FILE_PATH);

        $location = $this->service->uploadImage($uploadedFile);

        $this->assertSame(self::NEW_FILE_PATH, $location);
        Phake::verify($this->logger)->info('Uploading file ' . self::DEFAULT_FILE_PATH);
    }
}
