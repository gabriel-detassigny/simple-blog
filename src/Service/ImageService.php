<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use Psr\Http\Message\UploadedFileInterface;

class ImageService
{
    private const PUBLIC_DIRECTORY = __DIR__  . '/../../frontend/public';
    private const UPLOAD_PATH = '/images/upload/';

    public function uploadImage(UploadedFileInterface $uploadedFile): string
    {
        $uploadedFile->moveTo(self::PUBLIC_DIRECTORY . self::UPLOAD_PATH . $uploadedFile->getClientFilename());

        return self::UPLOAD_PATH . $uploadedFile->getClientFilename();
    }
}