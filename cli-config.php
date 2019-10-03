<?php
declare(strict_types=1);

use DI\ContainerBuilder;

require __DIR__ . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(
    __DIR__ . '/app/settings.php',
    __DIR__ . '/app/dependencies.php'
);
//$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
$container = $containerBuilder->build();

$entityManager = $container->get(\Doctrine\ORM\EntityManagerInterface::class);

//a

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
