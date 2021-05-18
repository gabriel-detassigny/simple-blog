<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service\Storage;

use Aws\S3\S3Client;
use GabrielDeTassigny\Blog\Service\Storage\S3Storage;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class S3StorageTest extends TestCase
{
    private const S3_BUCKET = 'test-bucket';
    private const FILENAME = 'filename.png';
    private const FILE_PATH = '/path/to/' . self::FILENAME;
    private const FILE_STREAM = 'some-binary-stream';

    /** @var S3Client|Phake_IMock */
    private $s3Client;

    /** @var S3Storage|Phake_IMock */
    private $s3Storage;

    /** @var UploadedFileInterface|Phake_IMock */
    private $file;

    protected function setUp(): void
    {
        $this->s3Client = Phake::mock(S3Client::class);
        $this->s3Storage = new S3Storage($this->s3Client, self::S3_BUCKET);

        $this->file = Phake::mock(UploadedFileInterface::class);
        Phake::when($this->file)->getClientFilename()->thenReturn(self::FILE_PATH);
        Phake::when($this->file)->getStream()->thenReturn(self::FILE_STREAM);
    }

    public function testStoreFileInS3Bucket(): void
    {
        $expectedUrl = 'https://aws.amazon.com/test-bucket/' . self::FILENAME;
        Phake::when($this->s3Client)->putObject(Phake::anyParameters())->thenReturn(['ObjectURL' => $expectedUrl]);

        $actual = $this->s3Storage->storeFile($this->file);

        $this->assertSame($expectedUrl, $actual);
        Phake::verify($this->s3Client)->putObject([
            'Bucket' => self::S3_BUCKET,
            'Key' => self::FILENAME,
            'Body' => self::FILE_STREAM,
            'ACL' => 'public-read'
        ]);
    }
}
