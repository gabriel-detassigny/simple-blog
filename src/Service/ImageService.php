<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service;

use GabrielDeTassigny\Blog\Service\Storage\StorageFactory;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;

class ImageService
{
    /** @var LoggerInterface */
    private $logger;

    /** @var StorageFactory */
    private $storageFactory;

    public function __construct(LoggerInterface $logger, StorageFactory $storageFactory)
    {
        $this->logger = $logger;
        $this->storageFactory = $storageFactory;
    }

    public function uploadImage(UploadedFileInterface $uploadedFile): string
    {
        $this->logger->info('Uploading file ' . $uploadedFile->getClientFilename());

        return $this->storageFactory->create()->storeFile($uploadedFile);
    }
}