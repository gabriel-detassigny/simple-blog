<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Tests\Service\Storage;

use GabrielDeTassigny\Blog\Service\Storage\FSStorage;
use GabrielDeTassigny\Blog\Service\Storage\S3Storage;
use GabrielDeTassigny\Blog\Service\Storage\StorageFactory;
use PHPUnit\Framework\TestCase;

class StorageFactoryTest extends TestCase
{
    /** @var StorageFactory */
    private $storageFactory;

    protected function setUp(): void
    {
        $this->storageFactory = new StorageFactory();
    }

    /**
     * @dataProvider storageProvider
     */
    public function testCreateReturnsExpectedStorage(array $env, string $expectedClass): void
    {
        foreach ($env as $key => $value) {
            putenv("$key=$value");
        }

        $storage = $this->storageFactory->create();

        $this->assertInstanceOf($expectedClass, $storage);
    }

    public function storageProvider(): array
    {
        return [
            'Storage type "s3" returns S3Storage' => [
                ['STORAGE_TYPE' => 's3', 'AWS_REGION' => 'eu-west-1', 'AWS_S3_BUCKET' => 'bucket'],
                S3Storage::class
            ],
            'Storage type "filesystem" returns FSStorage' => [
                ['STORAGE_TYPE' => 'filesystem'],
                FSStorage::class
            ],
            'Undefined storage type returns FSStorage' => [
                ['STORAGE_TYPE' => ''],
                FSStorage::class
            ]
        ];
    }
}
