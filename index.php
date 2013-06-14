<?php
/**
 * Version @version@
 * Точка входа для SiteForeverCMS
 * Этот файл вызывает сервер при запросах
 */

ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('display_errors', true);


// путь к фреймворку
// если не указан, то в текущей директории
define('SF_PATH', __DIR__);

//корень сайта
define('ROOT', __DIR__);

// автозагрузка классов
require_once SF_PATH.'/vendor/autoload.php';
//require_once SF_PATH . '/class/App.php';

$app = new App(preg_match('/^test/', $_SERVER['HTTP_HOST']) ? 'app/test.php' : 'app/development.php');
$app->run();

