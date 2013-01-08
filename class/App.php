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

set_error_handler( function ( $errno, $errstr) {
    throw new Exception( $errstr, $errno );
}, E_WARNING & E_NOTICE & E_DEPRECATED & E_USER_WARNING & E_USER_NOTICE & E_ERROR );

require_once 'Sfcms/Kernel/Base.php';

use Sfcms\Kernel\Base;
use Sfcms\Model;
use Sfcms\Request;
use Symfony\Component\HttpFoundation\Response;
use Sfcms\Controller\Resolver;
use Sfcms\View\Layout;
use Sfcms\View\Xhr;
use Sfcms\i18n;
use Sfcms\Model\Exception as ModelException;

/**
 * Класс приложение
 * FrontController
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://siteforever.ru
 */
class App extends Base
{
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
     * Инициализация
     * @static
     * @return void
     */
    public function init()
    {
        // Language
        $this->getConfig()->setDefault( 'language', 'en' );
        if ( ! $this->getRequest()->get('lang') ) {
            $this->getRequest()->set('lang', $this->getConfig('language'));
        } else {
            $this->getConfig()->set('language',$this->getRequest()->get('lang'));
        }
        i18n::getInstance()->setLanguage(
            $this->getConfig()->get( 'language' )
        );

        // TIME_ZONE
        date_default_timezone_set( 'Europe/Moscow' );

        // DB prefix
        if( ! defined( 'DBPREFIX' ) ) {
            if ( $this->getConfig()->get( 'db.prefix' ) ) {
                define( 'DBPREFIX', $this->getConfig()->get( 'db.prefix' ) );
            } else {
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
        $this->getSession();
//        ob_start();

        // маршрутизатор
        $this->getRouter()->routing();

        self::$init_time = microtime( 1 ) - self::$start_time;
        self::$controller_time = microtime( 1 );


        try {
            $result = $this->getResolver()->dispatch();
        } catch ( Exception $e ) {
            $error  = $this->getRequest()->setResponseError( $e );
            $result = $error['msg'];
        }

//        if ( is_string( $result ) ) {
//            $response = new Response( $result );
//        } elseif ( $result instanceof Response ) {
//            $response = $result;
//            $result   = $response->getContent();
//        }

//        if ( $this->getRequest()->getContent() && CACHE ) {
//            $this->getCacheManager()->setCache( $this->getRequest()->getContent() );
//            $this->getCacheManager()->save();
//        }
        $result = $this->prepareResult( $result );

        // Выполнение операций по обработке объектов
        try {
//            debugVar( Data_Watcher::instance()->dumpDirty(), 'dumpDirty' );
            Data_Watcher::instance()->performOperations();
        } catch ( ModelException $e ) {
            $response = $this->getRequest()->setResponseError( $e );
            $result .= $response['msg'];
        }

        // Redirect
        if ( $this->getRequest()->get('redirect') ) {
            if ( self::isTest() ) {
                print "Status: 301 Moved Permanently\n";
                print 'Location: '.$this->getRequest()->get('redirect')."\n";
            } else {
                header('Status: 301 Moved Permanently');
                header('Location: '.$this->getRequest()->get('redirect'));
            }
            return null;
        }

        // If result is image... This needing for captcha
        if ( is_resource( $result ) && imageistruecolor( $result ) ) {
            header('Content-type: image/png');
            imagepng( $result );
            return null;
        }

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

//        if ( ! $result ) { // Потом достаем из основного потока вывода
//            $result = ob_get_contents();
//        }
//        ob_end_clean();

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
        if( $this->getRequest()->getAjax() ) {
            $Layout = new Xhr( $this );
        } else {
            $Layout = new Layout( $this );
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
                Model::getDB()->saveLog();
            }
            $this->getLogger()->log(
                "Total SQL: " . count( Model::getDB()->getLog() )
                    . "; time: " . round( Model::getDB()->time, 3 ) . " sec.", 'app'
            );
            $this->getLogger()->log( "Init time: " . round( self::$init_time, 3 ) . " sec.", 'app' );
            $this->getLogger()->log( "Controller time: " . round( self::$controller_time, 3 ) . " sec.", 'app' );
            $exec_time = microtime( true ) - self::$start_time;
            $this->getLogger()->log(
                "Other time: " . round( $exec_time - self::$init_time - self::$controller_time, 3 ) . " sec.", 'app'
            );
            $this->getLogger()->log( "Execution time: " . round( $exec_time, 3 ) . " sec.", 'app' );
            $this->getLogger()->log( "Required memory: " . round( memory_get_peak_usage(true) / 1024, 3 ) . " kb.", 'app' );
        }
    }
}
