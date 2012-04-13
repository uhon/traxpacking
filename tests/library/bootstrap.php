<?php



use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ArrayInput;

use Symfony\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Symfony\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Symfony\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Symfony\Bundle\DoctrineFixturesBundle\Command\LoadDataFixturesDoctrineCommand;
use Symfony\Bundle\FrameworkBundle\Command\CacheWarmupCommand;

require_once APPLICATION_ENV .'/Bootstrap.php';
require_once __DIR__.'/AppKernel.php';

$kernel = new AppKernel('test', true); // create a "test" kernel
$kernel->boot();

$application = new Application($kernel);

// add the database:drop command to the application and run it
$command = new DropDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:database:drop',
    '--force' => true,
));
$command->run($input, new ConsoleOutput());

// add the database:create command to the application and run it
$command = new CreateDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:database:create',
));
$command->run($input, new ConsoleOutput());

// let Doctrine create the database schema (i.e. the tables)
$command = new CreateSchemaDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:schema:create',
));
$command->run($input, new ConsoleOutput());

//load fixtures
$command = new LoadDataFixturesDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:fixtures:load',
    '--env'   => 'test'
));
$command->run($input, new ConsoleOutput());

//warmup cache
$command = new CacheWarmupCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'cache:warmup',
    '--env'   => 'test'
));
$command->run($input, new ConsoleOutput());