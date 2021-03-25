<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
$container = require_once 'container.php';

$entityManager = $container->get(Doctrine\ORM\EntityManager::class); 
return ConsoleRunner::createHelperSet($entityManager);
