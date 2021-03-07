<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Storage;

use Psr\Http\Message\UploadedFileInterface;

class S3Storage implements StorageInterface
{

    public function storeFile(UploadedFileInterface $uploadedFile): string
    {
        // TODO: Implement storeFile() method.
    }
}