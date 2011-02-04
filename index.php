<?php
/**
 * $Id$
 * Точка входа для SiteForeverCMS
 * Этот файл вызывает сервер при запросах
 */
// путь к фреймворку
// если не указан, то в текущей директории
define('SF_PATH', dirname(__FILE__));

// версия php
define('CORRECT_PHP_VERSION', '5.2');

// директории для подключения
$include_list   = array();
if ( SF_PATH != dirname(__FILE__) ) {
    $include_list[] = dirname(__FILE__).DIRECTORY_SEPARATOR.'class';
    $include_list[] = dirname(__FILE__);
}
$include_list[] = SF_PATH.DIRECTORY_SEPARATOR.'class';
$include_list[] = SF_PATH;
$include_list[] = str_replace('.:', '', get_include_path());
set_include_path( join( PATH_SEPARATOR, $include_list ));
//die(get_include_path());
// запуск системы


require_once 'bootstrap.php';

$app = new App( dirname(__FILE__).'/protected/config/main.php');
$app->run();

