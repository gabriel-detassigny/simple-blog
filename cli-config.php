<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use GabrielDeTassigny\Blog\Container\WebContainerProvider;
use Symfony\Component\Yaml\Parser;

require_once __DIR__ . '/bootstrap.php';

$containerProvider = new WebContainerProvider(new Parser(), __DIR__ . '/config/container.yaml');
$container = $containerProvider->getContainer();
$entityManager = $container->get('entity_manager');

return ConsoleRunner::createHelperSet($entityManager);
