<?php
/**
 * Запус тестов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
// путь к фреймворку
// если не указан, то в текущей директории
defined('SF_PATH') || define('SF_PATH', realpath( dirname(__FILE__) . '/..' ));

//корень сайта
defined('ROOT') ||define('ROOT', SF_PATH );

$app    = new App('application/test.php');
$app->init();