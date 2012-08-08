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


// директории для подключения
$include_list   = array();
if ( SF_PATH != __DIR__ ) {
    $include_list[] = __DIR__.DIRECTORY_SEPARATOR.'class';
    $include_list[] = __DIR__;
}
$include_list[] = SF_PATH.DIRECTORY_SEPARATOR.'class';
$include_list[] = SF_PATH.DIRECTORY_SEPARATOR.'vendors';
$include_list[] = SF_PATH;
$include_list[] = str_replace('.:', '', get_include_path());
set_include_path( join( PATH_SEPARATOR, $include_list ));

// автозагрузка классов
require_once 'app.php';


$app = new App( __DIR__.'/protected/config/main.php');
$app->run();

