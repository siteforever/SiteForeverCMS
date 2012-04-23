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


// директории для подключения
$include_list   = array();
//if ( SF_PATH != dirname(__FILE__) ) {
//    $include_list[] = dirname(__FILE__).DIRECTORY_SEPARATOR.'class';
//    $include_list[] = dirname(__FILE__);
//}
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

$app    = new App( SF_PATH.'/protected/config/test.php' );
$app->init();