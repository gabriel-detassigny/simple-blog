<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$env = new Dotenv\Dotenv(__DIR__);
$env->load();

require_once __DIR__ . '/src/routes.php';
