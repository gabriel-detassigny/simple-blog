<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Storage;

use DateTime;

class StorageFactory
{
    public function create(): StorageInterface
    {
        switch (getenv('STORAGE_TYPE')) {
            case 's3':
                return new S3Storage();
            case 'filesystem':
            default:
                return new FSStorage(new DateTime());
        }
    }
}