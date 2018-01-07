<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use GabrielDeTassigny\Blog\Container\Container;
use GabrielDeTassigny\Blog\Container\WebContainerProvider;

require_once __DIR__ . '/bootstrap.php';

$container = WebContainerProvider::getContainer();
$entityManager = $container->get('entity_manager');

return ConsoleRunner::createHelperSet($entityManager);
