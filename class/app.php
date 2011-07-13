<?php

require_once 'application/abstract.php';

/**
 * Класс приложение
 * FronController
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
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

        if ( $this->getConfig()->get('db.debug') ) {
            Model::getDB()->saveLog();
        }

        if ( $this->getConfig()->get('debug.profile') ) {
            $this->logger->log("Total SQL: ".count( Model::getDB()->getLog())
                                . "; time: ".round( Model::getDB()->time, 3)." sec.", 'app');
            $this->logger->log("Init time: ".round(self::$init_time, 3)." sec.", 'app');
            $this->logger->log("Controller time: ".round(self::$controller_time, 3)." sec.", 'app');
            $exec_time  = microtime(true)-self::$start_time;
            $this->logger->log("Other time: ".round($exec_time-self::$init_time-self::$controller_time, 3)." sec.", 'app');
            $this->logger->log("Execution time: ".round($exec_time, 3)." sec.", 'app');
            $this->logger->log("Required memory: ".round(memory_get_usage() / 1024, 3)." kb.", 'app');
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
        // Language
        $this->getConfig()->setDefault('language', 'ru');
        translate::getInstance()->setLanguage(
            $this->getConfig()->get('language')
        );

        // Locale
        setlocale ( LC_TIME,    'rus', 'ru_RU.UTF-8', 'Russia');
        setlocale ( LC_NUMERIC, 'C', 'en_US.UTF-8', 'en_US', 'English');

        // TIME_ZONE
        date_default_timezone_set ( 'Europe/Moscow' );

        // DB prefix
        if ( ! defined( 'DBPREFIX' ) )
            if ( $this->getConfig()->get('db.prefix') ) {
                define('DBPREFIX', $this->getConfig()->get('db.prefix'));
            } else {
                define('DBPREFIX', '');
            }


        if ( defined('TEST') ) {
            require_once 'functionsTest.php';
        } else {
            require_once 'functions.php';
            $firephp = FirePHP::getInstance(true);
            $firephp->registerErrorHandler();
            $firephp->registerExceptionHandler();
        }

        if ( ! defined('MAX_FILE_SIZE') )
            define('MAX_FILE_SIZE', 2*1024*1024);
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

        // маршрутизатор
        $this->getRouter()->routing();

        // возможность использовать кэш
        if (    $this->getConfig()->get('caching')
           && ! $this->getRequest()->getAjax()
           && ! self::$ajax
           && ! $this->getRouter()->isSystem()
        ) {
            if (    $this->getRequest()->get('controller') == 'page'
                 && $this->getAuth()->currentUser()->perm == USER_GUEST
                 && $this->getBasket()->count() == 0
            ) {
                $this->getTpl()->caching(true);
            }
        }

        self::$init_time    = microtime(1) - self::$start_time;

        self::$controller_time  = microtime(1);
        $controller_resolver    = new ControllerResolver( $this );

        try {
            $result = $controller_resolver->dispatch();
        } catch ( ControllerException $e ) {
            $result = false;
            $this->getRequest()->setTemplate('inner');
            $this->getRequest()->setContent($e->getMessage());
        }
        self::$controller_time  = microtime(1) - self::$controller_time;

        $this->invokeView( $result );

        // Выполнение операций по обработке объектов
        Data_Watcher::instance()->performOperations();

        // Заголовок по-умолчанию
        if ( $this->getRequest()->getTitle() == '' ) {
            $this->getRequest()->setTitle( $this->getRequest()->get('tpldata.page.name'));
        }
    }

    /**
     * Вызвать отображение
     * @param mixed $result
     * @return void
     */
    function invokeView( $result )
    {
        /**
         * Данные шаблона
         */
        $this->getTpl()->assign( $this->getRequest()->get('tpldata') );
        $this->getTpl()->config      = $this->getConfig();
        $this->getTpl()->feedback    = $this->getRequest()->getFeedbackString();
        $this->getTpl()->host        = $_SERVER['HTTP_HOST'];
        $this->getTpl()->memory      = number_format( memory_get_usage() / 1024, 2, ',', ' ' ).' Kb';
        $this->getTpl()->exec        = number_format( microtime(true) - self::$start_time, 3, ',', ' ' ).' sec.';
        $this->getTpl()->request     = $this->getRequest();

        if ( ! $this->getRequest()->getAjax() )
        {
            header('Content-type: text/html; charset=utf-8');

            $theme_css  = $this->getRequest()->get('path.css');
            $theme_js   = $this->getRequest()->get('path.js');
            $path_misc  = $this->getRequest()->get('path.misc');

            if ( $this->getRequest()->get('resource') == 'system:' ) {
                // подключение админских стилей и скриптов

                $this->getRequest()->addStyle( $path_misc.'/smoothness/jquery-ui.css');
                $this->getRequest()->addStyle( $path_misc.'/admin/admin.css');
                // jQuery
                $this->getRequest()->addScript( $path_misc.'/jquery-ui.min.js' );
                $this->getRequest()->addScript( $path_misc.'/jquery.form.js' );
                //$request->addScript( $path_misc.'/jquery.cookie.js' );
                //$request->addScript( $path_misc.'/jquery.mousewheel-3.0.2.pack.js' );
                $this->getRequest()->addScript( $path_misc.'/jquery.blockUI.js' );

                switch ( strtolower( $this->getSettings()->get('editor','type') ) ) {
                    case 'tinymce':
                        // TinyMCE
                        $this->getRequest()->addScript( $path_misc.'/tinymce/jscripts/tiny_mce/jquery.tinymce.js' );
                        $this->getRequest()->addScript( $path_misc.'/admin/editor/tinymce.js' );
                        break;

                    case 'ckeditor':
                        // CKEditor
                        $this->getRequest()->addScript( $path_misc.'/ckeditor/ckeditor.js' );
                        $this->getRequest()->addScript( $path_misc.'/ckeditor/adapters/jquery.js' );
                        $this->getRequest()->addScript( $path_misc.'/admin/editor/ckeditor.js' );
                        break;

                    default: // plain
                }

                $this->getRequest()->addScript( $path_misc.'/forms.js' );
                $this->getRequest()->addScript( $path_misc.'/admin/catalog.js' );
                $this->getRequest()->addScript( $path_misc.'/admin/admin.js' );

            } else {
                if ( file_exists( trim( $theme_css, '/').'/style.css' ) ) {
                    $this->getRequest()->addStyle($theme_css.'/style.css');
                }
                if ( file_exists( trim( $theme_css, '/').'/print.css' ) ) {
                    $this->getRequest()->addStyle($theme_css.'/print.css');
                }
                if ( file_exists( trim( $theme_js.'/script.js', '/' ) ) ) {
                    $this->getRequest()->addScript($theme_js.'/script.js');
                }
            }

            $this->getTpl()->display( $this->getRequest()->get('resource').$this->getRequest()->get('template') );
        } else {
            // AJAX
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');

            if ( $this->getRequest()->getAjaxType() == Request::TYPE_JSON ) {
                header('Content-type: text/json; charset=utf-8');
                if ( $result ) {
                    if ( is_object( $result ) || is_array( $result ) ) {
                        $result = json_encode( $result );
                    } elseif ( is_string( $result ) ) {
                        if ( ! @json_decode( $result ) ) {
                            throw new Application_Exception('Result is not valid and can not convert to json');
                        }
                    }
                    print $result;
                } else {
                    print $this->getRequest()->getResponseAsJson();
                }
            }
            elseif ( $this->getRequest()->getAjaxType() == Request::TYPE_XML ) {
                header('Content-type: text/xml; charset=utf-8');
                print $this->getRequest()->getContent();
            }
            else {
                if ( count( $this->getRequest()->getFeedback() ) ) {
                    print '<div class="feedback">'.$this->getRequest()->getFeedbackString().'</div>';
                }
                if ( $this->getRequest()->getContent() ) {
                    print $this->getRequest()->getContent();
                }
            }
        }
    }

    /**
     * @static
     * @throws Exception
     * @param  $class_name
     * @return bool
     */
    static function autoload( $class_name )
    {
        static $class_count = 0;

        $class_name = strtolower( $class_name );

        if ( in_array( $class_name, array('finfo') ) ) {
            return false;
        }

        if ( $class_name == 'register' ) {
            throw new Exception('Autoload Register class');
        }

        // PEAR format autoload
        $file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ).'.php';

        if ( @include_once $file ) {
            if ( defined('DEBUG_AUTOLOAD') && DEBUG_AUTOLOAD ) {
                $class_count++;
                print "{$class_count}. include {$file}\n";
            }
            return true;
        }
        return false;
    }

}
