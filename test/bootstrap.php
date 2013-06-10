<?php
/**
 * Запус тестов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
// путь к фреймворку
// если не указан, то в текущей директории
defined('SF_PATH') || define('SF_PATH', realpath( dirname(__FILE__) . '/..' ));

//корень сайта
defined('ROOT') ||define('ROOT', SF_PATH );

use Symfony\Component\Process\Process;

//$mysqlFrom = "mysqldump -u root --add-drop-database=TRUE siteforever";
$mysqlTo = "mysql -u root siteforever_test";

//$process = new Process("$mysqlFrom | $mysqlTo");
$process = new Process("$mysqlTo < dump.sql");
$process->start();
while ($process->isRunning()) {
    sleep(1);
}
//if (!$process->isSuccessful()) {
//    die($process->getOutput());
//}

$app    = new App('application/test.php');
$app->init();
