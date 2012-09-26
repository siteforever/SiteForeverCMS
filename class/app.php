<?php
// директории для подключения
$include_list   = array();
if ( SF_PATH != ROOT ) {
    $include_list[] = ROOT.DIRECTORY_SEPARATOR.'class';
    $include_list[] = ROOT;
}
$include_list[] = SF_PATH.DIRECTORY_SEPARATOR.'class';
$include_list[] = SF_PATH.DIRECTORY_SEPARATOR.'vendors';
$include_list[] = SF_PATH;
$include_list[] = str_replace('.:', '', get_include_path());
set_include_path( join( PATH_SEPARATOR, $include_list ));

require_once 'functions.php';
require_once 'application/abstract.php';

/**
 * Класс приложение
 * FronController
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://siteforever.ru
 */
class App extends Application_Abstract
{

    static private $DEBUG;

    /**
     * Запуск приложения
     * @static
     * @return void
     */
    public function run()
    {
        self::$start_time = microtime( true );
        $this->init();
        $result = $this->handleRequest();
        $this->flushDebug();
        print $result;
    }

    /**
     * Run under test environment
     * @static
     * @return bool
     */
    static public function isTest()
    {
        return defined('TEST') && TEST;
    }

    /**
     * Run under development environment
     * @static
     * @return bool
     */
    static public function isDebug()
    {
        return App::$DEBUG;
    }

    /**
     * Инициализация
     * @static
     * @return void
     */
    public function init()
    {
        App::$DEBUG = $this->getConfig()->get( 'debug.profiler' );

        if( App::isDebug() ) {
            std_error::init( $this->getLogger() );
        }

        // Language
        $this->getConfig()->setDefault( 'language', 'en' );
        Sfcms_i18n::getInstance()->setLanguage(
            $this->getConfig()->get( 'language' )
        );

        // TIME_ZONE
        date_default_timezone_set( 'Europe/Moscow' );

        // DB prefix
        if( ! defined( 'DBPREFIX' ) ) {
            if( $this->getConfig()->get( 'db.prefix' ) ) {
                define( 'DBPREFIX', $this->getConfig()->get( 'db.prefix' ) );
            }
            else {
                define( 'DBPREFIX', '' );
            }
        }

        if( ! defined( 'MAX_FILE_SIZE' ) ) {
            define( 'MAX_FILE_SIZE', 2 * 1024 * 1024 );
        }
        $installer = new Sfcms_Installer();
        $installer->installationStatic();
    }


    /**
     * Обработка запросов
     * @static
     * @return mixed
     */
    protected function handleRequest()
    {
        // запуск сессии
        session_start();
        ob_start();

        $result = '';

        // маршрутизатор
        $this->getRouter()->routing();

        self::$init_time = microtime( 1 ) - self::$start_time;
        self::$controller_time = microtime( 1 );

        $resolver   = new \Sfcms\Controller\Resolver( $this );

        try {
            $result = $resolver->dispatch();
        } catch ( Sfcms_Http_Exception $e ) {
            if ( ! App::isTest() ) {
                switch ( $e->getCode() ) {
                    case 301:
                        header("{$_SERVER['SERVER_PROTOCOL']} 301 Moved Permanently");
                        break;
                    case 403:
                        header ("{$_SERVER['SERVER_PROTOCOL']} 403 Forbidden");
                        break;
                    case 404:
                        header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
                        break;
                }
            }
            $this->getRequest()->setResponseError( $e->getCode(), $e->getMessage() );
            $result = $e->getMessage();
        } catch ( Exception $e ) {
            if ( App::isDebug() ) {
                $this->getRequest()
                    ->setResponseError( $e->getCode(), $e->getMessage() . "\n" . $e->getTraceAsString() );
                $result = "<pre class='alert alert-error'><strong>"
                        . get_class( $e )."</strong> {$e->getMessage()}\n"
                        . ( App::isDebug()
                            ? "{$e->getFile()} line {$e->getLine()}\n{$e->getTraceAsString()}"
                            : '' )
                        . '</pre>';
            } else {
                $this->getRequest()->setResponseError( $e->getCode(), $e->getMessage() );
            }
        }

        // Выполнение операций по обработке объектов
        try {
            Data_Watcher::instance()->performOperations();
        } catch ( Sfcms_Model_Exception $e ) {
            $this->getRequest()->setResponseError( $e->getCode(), $e->getMessage() );
        }

        // Redirect
        if ( $this->getRequest()->get('redirect') ) {
            if ( App::isTest() ) {
                print 'location: '.$this->getRequest()->get('redirect');
            } else {
                header('Status: 301 Moved Permanently');
                header('Location: '.$this->getRequest()->get('redirect'));
            }
            return;
        }

        // If result is image. Need for captcha
        if ( is_resource( $result ) && imageistruecolor( $result ) ) {
            header('Content-type: image/png');
            imagepng( $result );
            return;
        }

        $result = $this->prepareResult( $result );

//        if ( $this->getRequest()->getContent() && CACHE ) {
//            $this->getCacheManager()->setCache( $this->getRequest()->getContent() );
//            $this->getCacheManager()->save();
//        }

        self::$controller_time = microtime( 1 ) - self::$controller_time;

        $result = $this->invokeLayout( $result );
        if ( $reload = $this->getRequest()->get('reload') ) {
            $result .= $reload;
        }
        return $result;
    }

    /**
     * Обработает и подготовит результат
     * @param $result
     * @return string
     */
    protected function prepareResult( $result )
    {
//        $this->getLogger()->log( $result, 'result' );
//        $this->getLogger()->log( $this->getRequest()->getResponse(), 'response' );
        if ( ! $result ) {
            // Сначала пытаемся достать из Response
            $result = $this->getRequest()->getResponse();
        }

        if ( ! $result ) { // Потом достаем из основного потока вывода
            $result = ob_get_contents();
        }
        ob_end_clean();

//        $this->getLogger()->log($this->getRequest()->getAjaxType(),'ajax type');
        if ( is_array( $result ) && Request::TYPE_JSON == $this->getRequest()->getAjaxType() ) {
            // Если надо вернуть JSON из массива
            $result = json_encode( $result );
        }
//        $this->getLogger()->log( $result, 'result' );

        // Имеет больший приоритет, чем данные в Request->content
        if ( is_array( $result ) ) {
            // Если надо отпарсить шаблон с данными из массива
            $this->getTpl()->assign( $result );
            $template   = $this->getRequest()->getController() . '.' . $this->getRequest()->getAction();
            $result = $this->getTpl()->fetch( $template );
        }

        if ( is_string( $result ) ) {
            // Просто установить итоговую строку как контент
            $this->getRequest()->setContent( $result );
        }
        return $result;
    }

    /**
     * Вызвать отображение
     * @param mixed $result
     *
     * @return mixed
     */
    protected function invokeLayout( $result )
    {
        if( ! $this->getRequest()->getAjax() ) {
            $Layout = new Sfcms_View_Layout( $this );
        } else {
            $Layout = new Sfcms_View_Xhr( $this );
        }
        return $Layout->view( $result );
    }


    /**
     * Flushing debug info
     */
    protected function flushDebug()
    {
        // todo
        // Вывод в консоль FirePHP вызывает исключение, если не включена буферизация вывода
        // Fatal error: Exception thrown without a stack frame in Unknown on line 0

        if ( App::isDebug() ) {
            if ( $this->getConfig()->get( 'db.debug' ) ) {
                Sfcms_Model::getDB()->saveLog();
            }
            $this->getLogger()->log(
                "Total SQL: " . count( Sfcms_Model::getDB()->getLog() )
                    . "; time: " . round( Sfcms_Model::getDB()->time, 3 ) . " sec.", 'app'
            );
            $this->getLogger()->log( "Init time: " . round( self::$init_time, 3 ) . " sec.", 'app' );
            $this->getLogger()->log( "Controller time: " . round( self::$controller_time, 3 ) . " sec.", 'app' );
            $exec_time = microtime( true ) - self::$start_time;
            $this->getLogger()->log(
                "Other time: " . round( $exec_time - self::$init_time - self::$controller_time, 3 ) . " sec.", 'app'
            );
            $this->getLogger()->log( "Execution time: " . round( $exec_time, 3 ) . " sec.", 'app' );
            $this->getLogger()->log( "Required memory: " . round( memory_get_peak_usage() / 1024, 3 ) . " kb.", 'app' );
        }
    }
}
