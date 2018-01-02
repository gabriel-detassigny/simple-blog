<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use GabrielDeTassigny\Blog\Container\Container;

require_once __DIR__ . '/bootstrap.php';

$container = new Container();
$entityManager = $container->get('entity_manager');

return ConsoleRunner::createHelperSet($entityManager);
