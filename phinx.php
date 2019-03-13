<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$dbEnv = 'development';
if (getenv('DB_DEV') === 'false') {
    $dbEnv = 'production';
}

$config = [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations'
    ],
    'environments' => [
        'default_database' => 'development',
        'default_migration_table' => 'phinxlog',
        $dbEnv => [
            'adapter' => 'mysql',
            'host' => getenv('DB_HOST'),
            'name' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'pass' => getenv('DB_PASSWORD'),
            'port' => 3306,
            'charset' => 'utf8'
        ]
    ]
];

return $config;
