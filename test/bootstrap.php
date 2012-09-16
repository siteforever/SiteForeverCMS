<?php
/**
 * Запус тестов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
// путь к фреймворку
// если не указан, то в текущей директории
define('SF_PATH', realpath( dirname(__FILE__) . '/..' ));

//корень сайта
define('ROOT', SF_PATH );

// Запущены тесты
define('TEST', true);

$_SERVER['HTTP_HOST'] = 'test';


// директории для подключения
$include_list   = array();
$include_list[] = SF_PATH.DIRECTORY_SEPARATOR.'class';
$include_list[] = SF_PATH.DIRECTORY_SEPARATOR.'vendors';
$include_list[] = SF_PATH;
$include_list[] = str_replace('.:', '', get_include_path());
set_include_path( join( PATH_SEPARATOR, $include_list ));

require_once 'PHPUnit/Autoload.php';

$_REQUEST['id'] = 1;
$_REQUEST['route']  = 'index';

// автозагрузка классов
require_once 'app.php';

$app    = new App( array('protected/config/main.php','protected/config/test.php') );
$app->init();