<?php
/**
 * Загрузка тестового окружения
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
//require_once 'PHPUnit/Framework/TestCase.php';

define('CONFIG', 'main');

// директории для подключения
set_include_path( join( PATH_SEPARATOR, array(
    realpath( dirname( __FILE__ ).'/../class' ),
    realpath( dirname( __FILE__ ).'/..' ),
    dirname( __FILE__ ),
    get_include_path(),
)));

function __autoload( $classname )
{
    //print "autoload: $classname\n";
    require_once str_replace('_', DIRECTORY_SEPARATOR, $classname).'.php';
}

?>