<?php
/**
 * $Id$
 * Точка входа для SiteForeverCMS
 * Этот файл вызывает сервер при запросах
 */

// Профиль конфигурации
define('CONFIG', '@config@');

// путь к фреймворку
define('SF_PATH', '@sf_path@');

// версия php
define('CORRECT_PHP_VERSION', '5.2');

// директории для подключения
set_include_path( join( PATH_SEPARATOR, array(
    dirname( __FILE__ ).DIRECTORY_SEPARATOR.'class',
    dirname( __FILE__ ),
    SF_PATH.DIRECTORY_SEPARATOR.'class',
    SF_PATH,
    get_include_path(),
)));

// запуск системы
require_once dirname( __FILE__ ).DIRECTORY_SEPARATOR.'bootstrap.php';

$app = new App();
$app->run();
