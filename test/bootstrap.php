<?php
/**
 * Запус тестов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
//корень сайта
defined('ROOT') || define('ROOT', realpath(__DIR__ . '/..'));

require_once 'vendor/autoload.php';

use Symfony\Component\Process\Process;

//$mysqlFrom = "mysqldump -u root --add-drop-database=TRUE siteforever";
$mysqlTo = "mysql -u root siteforever_test";

$process = new Process("$mysqlTo < ".__DIR__."/dump.sql");
print $process->getCommandLine() . PHP_EOL;
$process->start();
while ($process->isRunning()) {
    print "database restoring...\n";
    sleep(1);
}
if (!$process->isSuccessful()) {
    print_r($process->getErrorOutput());
    exit(255);
}

$process = new Process("php app/console --env=test database:scheme:update --force");
print $process->getCommandLine() . PHP_EOL;
$process->start();
while ($process->isRunning()) {
    sleep(1);
}
if (!$process->isSuccessful()) {
    print_r($process->getErrorOutput());
    exit(255);
}
print $process->getOutput();

$app = new App('test', true);
