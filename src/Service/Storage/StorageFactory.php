<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Service\Storage;

use Aws\S3\S3Client;
use DateTime;

class StorageFactory
{
    public function create(): StorageInterface
    {
        switch (getenv('STORAGE_TYPE')) {
            case 's3':
                return new S3Storage(
                    new S3Client(['version' => 'latest', 'region'  => getenv('AWS_REGION')]),
                    getenv('AWS_S3_BUCKET')
                );
            case 'filesystem':
            default:
                return new FSStorage(new DateTime());
        }
    }
}