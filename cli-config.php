<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use GabrielDeTassigny\Blog\Container\WebContainerProvider;

require_once __DIR__ . '/bootstrap.php';

$containerProvider = new WebContainerProvider(__DIR__ . '/config/container.yaml');
$container = $containerProvider->getContainer();
$entityManager = $container->get('entity_manager');

return ConsoleRunner::createHelperSet($entityManager);
