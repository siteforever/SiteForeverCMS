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

        if ( DEBUG_SQL ) {
            Model::getDB()->saveLog();
        }

        if ( $this->getConfig()->get('debug.profile') ) {
            $this->logger->log("Total SQL: ".count( Model::getDB()->getLog()).
                               "; time: ".round( Model::getDB()->time, 3)." sec.", 'app');
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
        $this->getConfig()->setDefault('language', 'ru');
        translate::getInstance()->setLanguage(
            $this->getConfig()->get('language')
        );
    }

    /**
     * Обработка запросов
     * @static
     * @return void
     */
    function handleRequest()
    {
        // маршрутизатор
        $this->getRouter()->routing();

        // возможность использовать кэш
        if (    $this->getConfig()->get('caching') &&
                ! $this->getRequest()->getAjax() &&
                ! self::$ajax &&
                ! $this->getRouter()->isSystem()
        ) {
            if (    $this->getRequest()->get('controller') == 'page' &&
                    $this->getAuth()->currentUser()->perm == USER_GUEST &&
                    $this->getBasket()->count() == 0
            ) {
                self::$tpl->caching(true);
            }
        }

        // если запрос является системным
        if ( $this->getRouter()->isSystem() )
        {
            if ( $this->getAuth()->currentUser()->perm == USER_ADMIN )
            {
                //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');
                $this->getRequest()->set('template', 'index' );
                $this->getRequest()->set('resource', 'system:');
                $this->getRequest()->set('modules', @include('modules.php'));
            }
            else {
                //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');
                $this->getRequest()->addFeedback( t('Protected page') );
                $this->getRequest()->set('controller', 'users');
                $this->getRequest()->set('action', 'login');
            }
        }

        self::$init_time    = microtime(1) - self::$start_time;

        self::$controller_time  = microtime(1);
        $controller_resolver    = new ControllerResolver( $this );
        try {
            $result = $controller_resolver->callController();
        } catch ( ControllerException $e ) {
            $result = false;
            $this->getRequest()->setContent($e->getMessage());
        }
        self::$controller_time  = microtime(1) - self::$controller_time;

        $this->invokeView( $result ); 

        // Выполнение операций по обработке объектов
        Data_Watcher::instance()->performOperations();

        // Заголовок по-умолчанию
        if ( self::$request->getTitle() == '' ) {
            self::$request->setTitle( self::$request->get('tpldata.page.name'));
        }
    }

    /**
     * Вызвать отображение
     * @return void
     */
    function invokeView( $result )
    {
        $request    = $this->getRequest();

        /**
         * Данные шаблона
         */
        $this->getTpl()->assign( self::$request->get('tpldata') );
        $this->getTpl()->config      = self::$config;
        $this->getTpl()->feedback    = self::$request->getFeedbackString();
        $this->getTpl()->host        = $_SERVER['HTTP_HOST'];
        $this->getTpl()->memory      = number_format( memory_get_usage() / 1024, 2, ',', ' ' ).' Kb';
        $this->getTpl()->exec        = number_format( microtime(true) - self::$start_time, 3, ',', ' ' ).' sec.';
        $this->getTpl()->request     = $request;

        if ( ! $request->getAjax() )
        {
            header('Content-type: text/html; charset=utf-8');

            $theme_css  = $request->get('path.css');
            $theme_js   = $request->get('path.js');
            $path_misc  = $request->get('path.misc');

            if ( $request->get('resource') == 'system:' ) {
                // подключение админских стилей и скриптов

                $request->addStyle( $path_misc.'/smoothness/jquery-ui.css');
                $request->addStyle( $path_misc.'/admin/admin.css');
                // jQuery
                $request->addScript( $path_misc.'/jquery-ui.min.js' );
                $request->addScript( $path_misc.'/jquery.form.js' );
                //$request->addScript( $path_misc.'/jquery.cookie.js' );
                //$request->addScript( $path_misc.'/jquery.mousewheel-3.0.2.pack.js' );
                $request->addScript( $path_misc.'/jquery.blockUI.js' );
                // CKEditor
                //$request->addScript( $path_misc.'/ckeditor/ckeditor.js' );
                //$request->addScript( $path_misc.'/ckeditor/adapters/jquery.js' );
                // TinyMCE
                $request->addScript( $path_misc.'/tinymce/jscripts/tiny_mce/jquery.tinymce.js' );


                $request->addScript( $path_misc.'/forms.js' );
                $request->addScript( $path_misc.'/admin/catalog.js' );
                $request->addScript( $path_misc.'/admin/admin.js' );

            } else {
                if ( file_exists( trim( $theme_css, '/').'/style.css' ) ) {
                    $request->addStyle($theme_css.'/style.css');
                }
                if ( file_exists( trim( $theme_css, '/').'/print.css' ) ) {
                    $request->addStyle($theme_css.'/print.css');
                }
                if ( file_exists( trim( $theme_js.'/script.js', '/' ) ) ) {
                    $request->addScript($theme_js.'/script.js');
                }
            }

            $this->getTpl()->display( $request->get('resource').$request->get('template') );
        } else {
            // AJAX
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');

            if ( $request->getAjaxType() == Request::TYPE_JSON ) {
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
                    /*print json_encode( array(
                        'error'     => $request->getError(),
                        'feedback'  => $request->getFeedback(),
                        'content'   => $request->getContent(),
                    ));*/
                }
            }
            elseif ( $request->getAjaxType() == Request::TYPE_XML ) {
                header('Content-type: text/xml; charset=utf-8');
                print $request->getContent();
            }
            else {
                if ( count( $request->getFeedback() ) ) {
                    print '<div class="feedback">'.$request->getFeedbackString().'</div>';
                }
                if ( $request->getContent() ) {
                    print $request->getContent();
                }
            }
        }
    }

    /**
     * Вернет модель
     * @param string $model
     * @return Model
     */
    function getModel( $model )
    {
        return Model::getModel( $model );
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
        $file = str_replace( '_', DS, $class_name ).'.php';

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
