<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use DateTime;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;

class ImageService
{
    private const PUBLIC_DIRECTORY = __DIR__  . '/../../frontend/public';
    private const UPLOAD_PATH = '/images/upload/';

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function uploadImage(UploadedFileInterface $uploadedFile, DateTime $time): string
    {
        $this->logger->info('Uploading file ' . $uploadedFile->getClientFilename());

        $imageInfo = pathinfo($uploadedFile->getClientFilename());
        $newFilename = $imageInfo['filename'] . '-' . $time->getTimestamp() . '.' . $imageInfo['extension'];

        $this->logger->info('Moving file to ' . $newFilename);
        $uploadedFile->moveTo(self::PUBLIC_DIRECTORY . self::UPLOAD_PATH . $newFilename);

        return self::UPLOAD_PATH . $newFilename;
    }
}