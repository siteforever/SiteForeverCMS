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
define('SF_PATH', dirname(__FILE__));

//корень сайта
define('ROOT', dirname(__FILE__));


// директории для подключения
$include_list   = array();
if ( SF_PATH != dirname(__FILE__) ) {
    $include_list[] = dirname(__FILE__).DIRECTORY_SEPARATOR.'class';
    $include_list[] = dirname(__FILE__);
}
$include_list[] = SF_PATH.DIRECTORY_SEPARATOR.'class';
$include_list[] = SF_PATH.DIRECTORY_SEPARATOR.'vendors';
$include_list[] = SF_PATH;
$include_list[] = str_replace('.:', '', get_include_path());
set_include_path( join( PATH_SEPARATOR, $include_list ));

// автозагрузка классов
require_once 'app.php';


$app = new App( dirname(__FILE__).'/protected/config/main.php');
$app->run();

