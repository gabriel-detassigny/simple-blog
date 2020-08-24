<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use GabrielDeTassigny\Blog\Container\WebContainerProvider;

require_once __DIR__ . '/bootstrap.php';

$containerProvider = new WebContainerProvider(__DIR__ . '/config/container.yaml');
$container = $containerProvider->getContainer();
$entityManager = $container->get(EntityManager::class);

return ConsoleRunner::createHelperSet($entityManager);
