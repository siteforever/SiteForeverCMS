<?php
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
     * Инициализация
     * @static
     * @return void
     */
    public function init()
    {
        define('DEBUG', $this->getConfig()->get( 'debug.profiler' ));

        if( DEBUG ) {
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

//        $installer = new Sfcms_Installer();
//        $installer->installationStatic();
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
        $controller_resolver   = new ControllerResolver( $this );

        try {
            $result = $controller_resolver->dispatch();
        } catch ( Exception $e ) {
            if ( DEBUG )
                $result = '<pre>'. $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
            else
                $result = $e->getMessage();
        }

        // Выполнение операций по обработке объектов
        Data_Watcher::instance()->performOperations();

        // Redirect
        if ( $this->getRequest()->get('redirect') ) {
            if ( defined('TEST') && TEST ) {
                return 'location: '.$this->getRequest()->get('redirect');
            } else {
                header('location: '.$this->getRequest()->get('redirect'));
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

        if ( $this->getRequest()->getContent() && CACHE ) {
            $this->getCacheManager()->setCache( $this->getRequest()->getContent() );
            $this->getCacheManager()->save();
        }

        // Заголовок по-умолчанию
        if( '' == $this->getRequest()->getTitle() ) {
            $this->getRequest()->setTitle( $this->getRequest()->get( 'tpldata.page.name' ) );
        }

        self::$controller_time = microtime( 1 ) - self::$controller_time;
        ob_end_clean();
        return $this->invokeView( $result );
    }

    /**
     * Обработает и подготовит результат
     * @param $result
     * @return string
     */
    protected function prepareResult( $result )
    {
        if ( ! $result ) { // Сначала пытаемся достать из Response
            $result = $this->getRequest()->getResponse();
        }
        if ( ! $result ) { // Потом достаем из основного потока вывода
            $result = ob_get_contents();
            ob_clean();
        }
        if ( is_array( $result ) && Request::TYPE_JSON == $this->getRequest()->getAjaxType() ) {
            // Если надо вернуть JSON из массива
            $result = json_encode( $result );
        }
        if ( is_array( $result ) && ! $this->getRequest()->getContent() ) {
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
     * @return void
     */
    protected function invokeView( $result )
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

        if ( DEBUG ) {
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
            }
            return true;
        }
        return false;
    }

}
