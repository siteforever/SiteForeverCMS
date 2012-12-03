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
$_SERVER['REQUEST_METHOD'] = 'TEST';

require_once 'PHPUnit/Autoload.php';

$_REQUEST['id'] = 1;
$_REQUEST['route']  = 'index';

// автозагрузка классов
require_once '../class/app.php';

$app    = new App( array('protected/config/development.php','protected/config/test.php') );
$app->init();