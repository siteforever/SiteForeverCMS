<?php
/**
 * Запус тестов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
// путь к фреймворку
// если не указан, то в текущей директории
defined('SF_PATH') || define('SF_PATH', realpath( __DIR__ . '/..' ));

//корень сайта
defined('ROOT') ||define('ROOT', SF_PATH );

require_once SF_PATH . '/vendor/autoload.php';

use Symfony\Component\Process\Process;

//$mysqlFrom = "mysqldump -u root --add-drop-database=TRUE siteforever";
$mysqlTo = "mysql -u root siteforever_test";

//$process = new Process("$mysqlFrom | $mysqlTo");
$process = new Process("$mysqlTo < ".__DIR__."/dump.sql");
var_dump($process->getCommandLine());
$process->start();
while ($process->isRunning()) {
    print "running...\n";
    sleep(1);
}
var_dump($process->getErrorOutput());
//if (!$process->isSuccessful()) {
//    die($process->getOutput());
//}

$app    = new App('application/test.php');
$app->init();
