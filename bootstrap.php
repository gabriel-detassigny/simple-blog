<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

session_start();
$env = new Dotenv\Dotenv(__DIR__);
$env->load();
