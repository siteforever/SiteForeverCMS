<?php
/**
 * Version @version@
 * Точка входа для SiteForeverCMS
 * Этот файл вызывает сервер при запросах
 */

// путь к фреймворку
// если не указан, то в текущей директории
define('SF_PATH', __DIR__);

//корень сайта
define('ROOT', __DIR__);

// автозагрузка классов
require_once SF_PATH.'/vendor/autoload.php';

$app = new App(
    isset($_SERVER['HTTP_HOST']) && preg_match('/^test/', $_SERVER['HTTP_HOST'])
    ? 'app/cfg/test.php' : 'app/cfg/development.php',
    true
);
$app->run();
