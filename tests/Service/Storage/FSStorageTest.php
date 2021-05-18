<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service\Storage;

use DateTime;
use GabrielDeTassigny\Blog\Service\Storage\FSStorage;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class FSStorageTest extends TestCase
{
    private const TIMESTAMP = 123456789;
    private const FILE_PATH = '/path/to/filename.png';

    /** @var FSStorage */
    private $fsStorage;

    /** @var UploadedFileInterface|Phake_IMock */
    private $file;

    protected function setUp(): void
    {
        $time = Phake::mock(DateTime::class);
        Phake::when($time)->getTimestamp()->thenReturn(self::TIMESTAMP);

        $this->fsStorage = new FSStorage($time);

        $this->file = Phake::mock(UploadedFileInterface::class);
        Phake::when($this->file)->getClientFilename()->thenReturn(self::FILE_PATH);
    }

    public function testStoreFileMovesItToPublicFolder(): void
    {
        $actual = $this->fsStorage->storeFile($this->file);

        $this->assertSame('/images/upload/filename-123456789.png', $actual);
        Phake::verify($this->file)->moveTo(Phake::anyParameters());
    }
}
