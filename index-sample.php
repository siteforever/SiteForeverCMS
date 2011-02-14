<?php
/**
 * $Id$
 * Точка входа для SiteForeverCMS
 * Этот файл вызывает сервер при запросах
 */

// путь к фреймворку
define('SF_PATH', '@sf_path@');

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

// запуск системы
require_once dirname( __FILE__ ).DIRECTORY_SEPARATOR.'bootstrap.php';

$app = new App( dirname(__FILE__).'/protected/config/@config@.php' );
$app->run();
