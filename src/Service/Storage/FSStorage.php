<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Storage;

use DateTime;
use Psr\Http\Message\UploadedFileInterface;

class FSStorage implements StorageInterface
{
    private const PUBLIC_DIRECTORY = __DIR__  . '/../../frontend/public';
    private const UPLOAD_PATH = '/images/upload/';

    /** @var DateTime */
    private $time;

    public function __construct(DateTime $time)
    {
        $this->time = $time;
    }

    public function storeFile(UploadedFileInterface $uploadedFile): string
    {
        $imageInfo = pathinfo($uploadedFile->getClientFilename());
        $newFilename = $imageInfo['filename'] . '-' . $this->time->getTimestamp();
        if (isset($imageInfo['extension'])) {
            $newFilename .= '.' . $imageInfo['extension'];
        }

        $uploadedFile->moveTo(self::PUBLIC_DIRECTORY . self::UPLOAD_PATH . $newFilename);

        return self::UPLOAD_PATH . $newFilename;
    }
}