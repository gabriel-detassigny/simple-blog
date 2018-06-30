<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service;

use DateTime;
use GabrielDeTassigny\Blog\Service\ImageService;
use Phake;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;

class ImageServiceTest extends TestCase
{
    private const TIMESTAMP = 1530344927;

    /** @var ImageService */
    private $service;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $logger = Phake::mock(LoggerInterface::class);
        $this->service = new ImageService($logger);
    }

    public function testUploadImage()
    {
        $time = new DateTime();
        $time->setTimestamp(self::TIMESTAMP);
        $uploadedFile = Phake::mock(UploadedFileInterface::class);
        Phake::when($uploadedFile)->getClientFilename()->thenReturn('/tmp/testimage.png');

        $location = $this->service->uploadImage($uploadedFile, $time);

        $this->assertSame('/images/upload/testimage-' . self::TIMESTAMP . '.png', $location);
        Phake::verify($uploadedFile)->moveTo(Phake::anyParameters());
    }
}
