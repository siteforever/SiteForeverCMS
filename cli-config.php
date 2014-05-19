<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Module\Doctrine\Manager\DoctrineManager;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Debug\Debug;

require_once 'vendor/autoload.php';

define('ROOT', __DIR__);

$input = new ArgvInput();
$env = $input->getParameterOption(array('--env', '-e'), getenv('SYMFONY_ENV') ?: 'dev');
$debug = getenv('SYMFONY_DEBUG') !== '0' && !$input->hasParameterOption(array('--no-debug', '')) && $env !== 'prod';

Debug::enable();
$kernel = new \App($env, $debug);
$kernel->isConsole(true);
/** @var DoctrineManager $doctrineManager */
$doctrineManager = $kernel->getContainer()->get('doctrine.manager');

// replace with mechanism to retrieve EntityManager in your app
$entityManager = $doctrineManager->getEntityManager();

$helperSet = ConsoleRunner::createHelperSet($entityManager);
$helperSet->set(new DialogHelper(), 'dialog');

$commands = array_merge($commands, [
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand()
]);

return $helperSet;
