<?php
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

        // включить лог SQL-запросов

        // WARNING!!!
        // Вывод в консоль FirePHP вызывает исключение, если не включена буферизация вывода
        // Fatal error: Exception thrown without a stack frame in Unknown on line 0
        if ( DEBUG_SQL ) {
            self::$db->saveLog();
        }
        //if ( App::$basket ) {
        //    App::$basket->save();
        //}
        if ( DEBUG_BENCHMARK ) {
            $this->logger->log("Total SQL: ".count(self::$db->getLog()).
                               "; time: ".round(self::$db->time, 3)." sec.", 'app');
            $this->logger->log("Execution time: ".round(microtime(true)-self::$start_time, 3)." sec.", 'app');
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
    protected function init()
    {
        // Конфигурация
        self::$config   = new SysConfig();

        $this->logger   = new Logger_Firephp();
        //$this->logger   = new Logger_Html();
        //$this->logger   = new Logger_Blank();

        // база данных
        if ( self::$config->get('db') ) {
            self::$db   = db::getInstance( self::$config->get('db') );
            self::$db->setLoggerClass( $this->logger );
        }

        // маршрутизатор
        self::$router   = new Router( $this->getRequest() );

        //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');
        
        // модель структуры
        self::$structure = $this->getModel('Structure');// Model::getModel('Structure');
        //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');

        // Авторизация
        $this->setAuthFormat('Session');

        // Пользователь
        self::$user     = $this->getAuth()->currentUser();

        //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');

        // модель для работы с шаблонами из базы
        // @TODO Подумать над Lazy Load в model_Templates
        //self::$templates= Model::getModel('model_Templates');

        translate::getInstance()->setLanguage('ru');
    }

    /**
     * Обработка запросов
     * @static
     * @return void
     */
    protected function handleRequest()
    {
        // маршрутизация
        self::$router->routing();
        self::$request->set('resource', 'theme:');

        //
        //  Настройки кэширования
        //
        $cache_id = self::$request->get('controller').self::$request->get('id');

        // возможность использовать кэш
        if (    TPL_CACHING &&
                ! self::$request->getAjax() &&
                ! self::$ajax &&
                ! self::$router->isSystem()
        ) {
            if (    self::$request->get('controller') == 'page' &&
                    self::$user->perm == USER_GUEST &&
                    self::$basket->count() == 0
            ) {
                self::$tpl->caching(true);
            }
        }
        //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');


        // если запрос является системным
        if ( self::$router->isSystem() )
        {
            if ( self::$user->perm == USER_ADMIN )
            {
                //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');
                self::$request->set('template', 'index' );
                self::$request->set('resource', 'system:');
                self::$request->set('modules', @include('modules.php'));
            }
            else {
                //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');
                self::$request->addFeedback( t('Protected page') );
                self::$request->set('controller', 'users');
                self::$request->set('action', 'login');
            }
        }

        //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');


        //print $controller_class.'::'.$action;
        //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');

        $controller_resolver    = new ControllerResolver();
        $controller_resolver->callController( $this );

        // Выполнение операций по обработке объектов
        Data_Watcher::instance()->performOperations();
        //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');

        //self::$request->debug();

        // Заголовок по-умолчанию
        if ( self::$request->getTitle() == '' ) {
            self::$request->setTitle( self::$request->get('tpldata.page.name'));
        }

        /**
         * Данные шаблона
         */
        $this->getTpl()->assign( self::$request->get('tpldata') );
        $this->getTpl()->config      = self::$config;
        $this->getTpl()->feedback    = self::$request->getFeedbackString();
        $this->getTpl()->host        = $_SERVER['HTTP_HOST'];
        $this->getTpl()->memory      = number_format( memory_get_usage() / 1024, 2, ',', ' ' ).' Kb';
        $this->getTpl()->exec        = number_format( microtime(true) - self::$start_time, 3, ',', ' ' ).' sec.';
        $this->getTpl()->request     = $this->getRequest();

        if ( ! self::$ajax )
        {
            header('Content-type: text/html; charset=utf-8');
            $this->getTpl()->display( self::$request->get('resource').self::$request->get('template'), $cache_id );
        } else {
            // AJAX
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            if ( $this->getRequest()->getAjaxType() == Request::TYPE_JSON ) {
                if ( $return ) {
                    print $return;
                } else {
                    print json_encode( array(
                        'error'     => $this->getRequest()->getError(),
                        'feedback'  => $this->getRequest()->getFeedback(),
                        'content'   => $this->getRequest()->getContent(),
                    ));
                }
            }
            else {
                print $this->getRequest()->getFeedbackString();
                if ( $this->getRequest()->getContent() ) {
                    print $this->getRequest()->getContent();
                }
            }
        }
        //die( __FILE__.':'.__LINE__.'->'.__METHOD__.'()');
        //printVar( Data_Watcher::instance()->dumpAll() );
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
            if ( DEBUG_AUTOLOAD ) {
                self::getInstance()->getLogger()->log( $file, 'include '.++$class_count );
            }
            return true;
        }
        return false;
    }

}
