<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Storage;

use Psr\Http\Message\UploadedFileInterface;

interface StorageInterface
{
    public function storeFile(UploadedFileInterface $uploadedFile): string;
}