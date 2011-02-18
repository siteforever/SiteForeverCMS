<?php
/**
 * Запуск всех тестов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

define('TEST', true);

if ( ! defined('PHPUnit_MAIN_METHOD') ) {
    define('PHPUnit_MAIN_METHOD', 'Test::main');
}

define('CORRECT_PHP_VERSION', '5.2.0');



define('SF_PATH', realpath( dirname(__FILE__).DIRECTORY_SEPARATOR.'..' ));

$include_list[] = SF_PATH.DIRECTORY_SEPARATOR.'class';
$include_list[] = SF_PATH.DIRECTORY_SEPARATOR.'test';
$include_list[] = SF_PATH;
$include_list[] = str_replace('.:', '', get_include_path());
set_include_path( join( PATH_SEPARATOR, $include_list ));

require_once 'PHPUnit/Autoload.php';

$_REQUEST['id'] = 1;
$_REQUEST['route']  = 'index';

require_once 'bootstrap.php';
$app    = new App( SF_PATH.'/protected/config/test.php' );

require_once 'SysConfigTest.php';
require_once 'RequestTest.php';
require_once 'AppTest.php';
require_once 'ControllerTest.php';
require_once 'BasketTest.php';
require_once 'Data_CriteriaTest.php';
require_once 'ModelTest.php';
require_once 'RouterTest.php';




class Test {

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run( self::suite() );
    }

    public static function suite()
    {
        $ts = new PHPUnit_Framework_TestSuite( 'Test Classes' );
        $ts->addTestSuite('SysConfigTest');
        $ts->addTestSuite('RequestTest');
        $ts->addTestSuite('Data_CriteriaTest');
        $ts->addTestSuite('ModelTest');

        $ts->addTestSuite('RouterTest');

        $ts->addTestSuite('AppTest');
        $ts->addTestSuite('ControllerTest');
        $ts->addTestSuite('BasketTest');



        return $ts;
    }
}



if ( PHPUnit_MAIN_METHOD == 'Test::main' ) {
    Test::main();
}
