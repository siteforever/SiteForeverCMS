<?php
/**
 * Файл начальной загрузки параметров для SiteForeverCMS
 */

/*
 * Проверка версии PHP
 */
if ( version_compare(phpversion(), CORRECT_PHP_VERSION, '=' ) == true) {
    die ('PHP '.CORRECT_PHP_VERSION.' Only');
}

//корень сайта
define('ROOT', dirname(__FILE__));
// разделитель директорий
define('DS', DIRECTORY_SEPARATOR);

// отладка загрузки классов
define('DEBUG_AUTOLOAD', 1);

// Отладка запросов
define('DEBUG_SQL', true);

// Вывод бенчмарка
define('DEBUG_BENCHMARK', true);

define('TPL_CACHING', false);
define('TPL_CACHE_LIVETIME', 600);

define('REWRITEURL', true);

define('MAX_FILE_SIZE', 2*1024*1024);

define('DBPREFIX', '');
/*
define('DBSTRUCTURE',   DBPREFIX.'structure'); // таблица структуры
define('DBROUTES',      DBPREFIX.'routes');    // таблица статей
define('DBUSERS',       DBPREFIX.'users');     // таблица пользователей
define('DBSETTINGS',    DBPREFIX.'settings');  // таблица настроек

define('DBCATALOG',     DBPREFIX.'catalog');  // таблица настроек
define('DBCATGALLERY',  DBPREFIX.'catalog_gallery');  // таблица настроек

define('DBNEWS',        DBPREFIX.'news');       // список новостей
define('DBNEWSCATS',    DBPREFIX.'news_cats');  // список категорий новостей

define('DBORDER',       DBPREFIX.'order');      // список заказов
define('DBORDERPOS',    DBPREFIX.'order_positions'); // позиции заказов
define('DBORDERSTATUS', DBPREFIX.'order_statuses'); // позиции заказов
*/
// группы пользователей
define('USER_GUEST', '0'); // гость
define('USER_USER',  '1'); // юзер
define('USER_WHOLE', '2'); // оптовый покупатель
define('USER_ADMIN', '10'); // админ




// TIME_ZONE
date_default_timezone_set ( 'Europe/Moscow' );
// Locale
setlocale ( LC_TIME,    'rus', 'ru_RU.UTF-8', 'Russia');
setlocale ( LC_NUMERIC, 'C', 'en_US.UTF-8', 'en_US', 'English');

// запуск сессии
session_start();


// автозагрузка классов
require_once 'loader.php';
require_once 'functions.php';

$firephp = FirePHP::getInstance(true);
$firephp->registerErrorHandler();
$firephp->registerExceptionHandler();
