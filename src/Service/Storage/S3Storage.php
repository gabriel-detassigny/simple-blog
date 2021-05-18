<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Storage;

use Aws\S3\S3Client;
use Psr\Http\Message\UploadedFileInterface;

class S3Storage implements StorageInterface
{
    /** @var S3Client */
    private $s3Client;

    /** @var string */
    private $s3Bucket;

    public function __construct(S3Client $s3Client, string $s3Bucket)
    {
        $this->s3Client = $s3Client;
        $this->s3Bucket = $s3Bucket;
    }

    public function storeFile(UploadedFileInterface $uploadedFile): string
    {
        $imageInfo = pathinfo($uploadedFile->getClientFilename());

        $fileName = $imageInfo['filename'];
        if (isset($imageInfo['extension'])) {
            $fileName .= '.' . $imageInfo['extension'];
        }

        $result = $this->s3Client->putObject([
            'Bucket' => $this->s3Bucket,
            'Key' => $fileName,
            'Body' => $uploadedFile->getStream(),
            'ACL' => 'public-read'
        ]);

        return $result['ObjectURL'];
    }
}