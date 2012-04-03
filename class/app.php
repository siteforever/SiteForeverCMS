<?php
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
    /**
     * Запуск приложения
     * @static
     * @return void
     */
    function run()
    {
        ob_start();
        self::$start_time = microtime( true );

        $this->init();

        $this->handleRequest();

        // WARNING!!!
        // Вывод в консоль FirePHP вызывает исключение, если не включена буферизация вывода
        // Fatal error: Exception thrown without a stack frame in Unknown on line 0
        if( $this->getConfig()->get( 'db.debug' ) ) {
            Sfcms_Model::getDB()->saveLog();
        }

        if( $this->getConfig()->get( 'debug.profiler' ) ) {
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
            $this->getLogger()->log( "Required memory: " . round( memory_get_usage() / 1024, 3 ) . " kb.", 'app' );
        }
        ob_end_flush();
        //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');
    }

    /**
     * Инициализация
     * @static
     * @return void
     */
    function init()
    {
        if( $this->getConfig()->get( 'db.debug' ) ) {
            std_error::init( $this->getLogger() );
        }

        // Language
        $this->getConfig()->setDefault( 'language', 'ru' );
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


        require_once 'functions.php';

        if( ! defined( 'MAX_FILE_SIZE' ) ) {
            define( 'MAX_FILE_SIZE', 2 * 1024 * 1024 );
        }

        $this->installationStatic();
    }


    /**
     * Инсталлирует каталоги со статикой, если еще не инсталлированы
     */
    private function installationStatic()
    {
        if( ! is_dir( ROOT . DIRECTORY_SEPARATOR . 'images' ) ) {
            $this->copyDir( SF_PATH . DIRECTORY_SEPARATOR . 'images', ROOT . DIRECTORY_SEPARATOR . 'images' );
            $this->getLogger()->log('Created ' . ROOT . DIRECTORY_SEPARATOR . 'images');
        }
        if( ! is_dir( ROOT . DIRECTORY_SEPARATOR . 'misc' ) ) {
            $this->copyDir( SF_PATH . DIRECTORY_SEPARATOR . 'misc', ROOT . DIRECTORY_SEPARATOR . 'misc' );
            $this->getLogger()->log('Created ' . ROOT . DIRECTORY_SEPARATOR . 'misc');
        }
        if( ! is_dir( ROOT . DIRECTORY_SEPARATOR. '_runtime' . DIRECTORY_SEPARATOR . 'sxd' ) ) {
            $this->copyDir( SF_PATH . DIRECTORY_SEPARATOR . 'vendors' . DIRECTORY_SEPARATOR . 'sxd',
                ROOT . DIRECTORY_SEPARATOR. '_runtime' . DIRECTORY_SEPARATOR . 'sxd' );
            $this->getLogger()->log('Created ' . ROOT . DIRECTORY_SEPARATOR. '_runtime' . DIRECTORY_SEPARATOR . 'sxd' );
        }
    }

    /**
     * Создание рекурсивной копии каталога
     * @param $from
     * @param $to
     *
     * @return void
     */
    private function copyDir( $from, $to )
    {
        if( ! is_dir( $to ) ) {
            @mkdir( $to, 0755, 1 );
        }
        $files = glob( $from . DIRECTORY_SEPARATOR . '*' );
        foreach( $files as $file ) {
            if( is_dir( $file ) ) {
                $this->copyDir( $file, $to . DIRECTORY_SEPARATOR . basename( $file ) );
            }
            elseif( is_file( $file ) ) {
                @copy( $file, $to . DIRECTORY_SEPARATOR . basename( $file ) );
            }
        }
    }

    /**
     * Обработка запросов
     * @static
     * @return void
     */
    function handleRequest()
    {
        // запуск сессии
        session_start();

        $result = '';

        // маршрутизатор
        $this->getRouter()->routing();

        // возможность использовать кэш
        if( $this->getConfig()->get( 'caching' )
            && ! $this->getRequest()->getAjax()
            && ! self::$ajax
            && ! $this->getRouter()->isSystem()
        ) {
            if( $this->getRequest()->get( 'controller' ) == 'page'
                && $this->getAuth()->currentUser()->get('perm') == USER_GUEST
                && $this->getBasket()->count() == 0
            ) {
                $this->getTpl()->caching( true );
            }
        }

        self::$init_time = microtime( 1 ) - self::$start_time;

        self::$controller_time = microtime( 1 );
        $controller_resolver   = new ControllerResolver( $this );

        try {
            $result = $controller_resolver->dispatch();
            if ( ! $this->getRequest()->getContent() ) {
                if ( is_array( $result ) ) {
                    $this->getTpl()->assign( $result );
                    $template   = $this->getRequest()->getController() . '.' . $this->getRequest()->getAction();
                    $this->getRequest()->setContent( $this->getTpl()->fetch( $template ) );
                } else if ( is_string( $result ) ) {
                    $this->getRequest()->setContent( $result );
                }
            }
        }
        catch( ControllerException $e ) {
            $result = false;
            $this->getRequest()->setTemplate( 'inner' );
            $this->getRequest()->setContent( $e->getMessage() );
        }
        catch( Exception $e ) {
            $this->getRequest()->setTemplate( 'inner' );
            $this->getRequest()->setContent( $e->getMessage() );
        }
        self::$controller_time = microtime( 1 ) - self::$controller_time;
        $this->invokeView( $result );

        // Выполнение операций по обработке объектов
        Data_Watcher::instance()->performOperations();

        // Заголовок по-умолчанию
        if( '' == $this->getRequest()->getTitle() ) {
            $this->getRequest()->setTitle( $this->getRequest()->get( 'tpldata.page.name' ) );
        }
    }

    /**
     * Вызвать отображение
     * @param mixed $result
     *
     * @return void
     */
    function invokeView( $result )
    {
        if( ! $this->getRequest()->getAjax() ) {
            $Layout = new Sfcms_View_Layout( $this );
        }
        else {
            $Layout = new Sfcms_View_Xhr( $this );
        }
        print $Layout->view( $result );
    }

    /**
     * @static
     * @throws Exception
     *
     * @param  $class_name
     *
     * @return boolean
     */
    static function autoload( $class_name )
    {
        static $class_count = 0;

        $class_name = strtolower( $class_name );

        if( in_array( $class_name, array( 'finfo' ) ) ) {
            return false;
        }

        if( $class_name == 'register' ) {
            throw new Exception( 'Autoload Register class' );
        }


        // PEAR format autoload
        $class_name = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, $class_name );
        $class_name = str_replace( '_', DIRECTORY_SEPARATOR, $class_name );
        $file       = $class_name . '.php';

        if( @include_once $file ) {
            if( defined( 'DEBUG_AUTOLOAD' ) && DEBUG_AUTOLOAD ) {
                $class_count ++;
                print "{$class_count}. include {$file}\n";
            }
            return true;
        }
        return false;
    }

}
