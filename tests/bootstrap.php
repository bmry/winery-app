<?php

use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
/**
 * Created by PhpStorm.
 * User: Morayo
 * Date: 2/28/2019
 * Time: 6:28 PM
 */

// tests/bootstrap.php
if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
    // executes the "php bin/console cache:clear" command
    passthru(sprintf(
        'APP_ENV=%s php "%s/../bin/console" cache:clear --no-warmup',
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'],
        __DIR__
    ));
}

require __DIR__.'/../vendor/autoload.php';

$kernel = new \App\Kernel('test', true); // create a "test" kernel
$kernel->boot();

$application = new Application($kernel);

$container = $kernel->getContainer();
$entityManager = $container->get('doctrine.orm.entity_manager');

$command = new DropDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:database:drop',
    '--force' => true
));
$command->run($input, new ConsoleOutput());


$command = new CreateDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:database:create',
));
$command->run($input, new ConsoleOutput());

$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
$classes = $entityManager->getMetadataFactory()->getAllMetadata();
$schemaTool->createSchema($classes);


$command = new \App\Command\RSSFeedLoaderCommand($entityManager);
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'app:load_rss_feed'
));

$command->run($input, new ConsoleOutput());


