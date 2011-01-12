<?php
/**
 * Класс приложение
 * FronController
 * @author KelTanas
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
        //ob_start();
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
            $this->logger->log("Total SQL time: ".round(self::$db->time, 3)." sec.", 'app');
            $this->logger->log("Execution time: ".round(microtime(true)-self::$start_time, 3)." sec.", 'app');
            $this->logger->log("Required memory: ".round(memory_get_usage() / 1024, 3)." kb.", 'app');
        }

        ob_end_flush();
    }

    /**
     * Инициализация
     * @static
     * @return void
     */
    function init()
    {
        // Конфигурация
        self::$config   = new SysConfig();

        //обработка ошибок
        // инициализируется в bootstrap.php через класс FirePHP
        //Error::init();
        // шаблонизатор
        self::$tpl      = Tpl_Factory::create();

        $this->logger   = new logger();

        // база данных
        if ( self::$config->get('db') ) {
            self::$db       = db::getInstance(self::$config->get('db'));
            self::$db->setLoggerClass( $this->logger );
        }

        // канал запросов
        self::$request  = new Request();
        self::$ajax = self::$request->getAjax();

        // модель структуры
        //self::$structure = Model::getModel('model_Structure');
        self::$structure = Model::getModel('model_Structure');

        // маршрутизатор
        self::$router   = new Router( self::$request );
        // Пользователь

        self::$user     = model_User::getCurrentUser();

        // корзина
        self::$basket   = basketFactory::createBasket( self::$user );
        //die(__CLASS__.'::'.__FUNCTION__.'('.__LINE__.')');

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
    function handleRequest()
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
            //self::$request->debug();
            if (    self::$request->get('controller') == 'page' &&
                    self::$user->getPermission() == USER_GUEST &&
                    self::$basket->count() == 0
            ) {
                self::$tpl->caching(true);
            }
        }

        // если запрос является системным
        if ( self::$router->isSystem() )
        {
            if ( self::$user->getPermission() == USER_ADMIN )
            {
                self::$request->set('template', 'index' );
                self::$request->set('resource', 'system:');
                self::$request->set('modules', @include('modules.php'));
            }
            else {
                self::$request->addFeedback( t('Protected page') );
                self::$request->set('controller', 'users');
                self::$request->set('action', 'login');
            }
        }


        $controller_class   = 'controller_'.self::$request->get('controller');
        $action             = self::$request->get('action').'Action';

	//print $controller_class.'::'.$action;

        if ( class_exists( $controller_class ) )
        {
            $reflection_class = new ReflectionClass( $controller_class );

            $controller = new $controller_class();

            if ( $reflection_class->hasMethod( 'init' ) ) {
                $controller->init();
            }

            if ( $reflection_class->hasMethod( $action ) ) {
                $controller->$action();
            }
            elseif ( $reflection_class->hasMethod( 'indexAction' ) ) {
                $controller->indexAction();
            }
            else {
                throw new Exception(t('Could not start the controller').' '.$controller_class);
            }
        }
        else {
            throw new Exception(t('Unable to find controller').' '.$controller_class);
        }

        //self::$request->debug();

        // Заголовок по-умолчанию
        if ( self::$request->getTitle() == '' ) {
            self::$request->setTitle( self::$request->get('tpldata.page.name'));
        }

        /**
         * Данные шаблона
         */
        self::$tpl->assign( self::$request->get('tpldata') );
        self::$tpl->config      = self::$config;
        self::$tpl->feedback    = self::$request->getFeedbackString();
        self::$tpl->host        = $_SERVER['HTTP_HOST'];
        self::$tpl->memory      = number_format( memory_get_usage() / 1024, 2, ',', ' ' ).' Kb';
        self::$tpl->exec        = number_format( microtime(true) - self::$start_time, 3, ',', ' ' ).' sec.';
        self::$tpl->request     = self::$request;

        //Error::dump($_SERVER);

        // HTTP_X_REQUESTED_WITH => XMLHttpRequest
        if ( ! self::$ajax )
        {
            header('Content-type: text/html; charset=utf-8');
            self::$tpl->display( self::$request->get('resource').self::$request->get('template'), $cache_id );
            /*if ( self::$tpl->is_cached($tpl_resource.self::$request->get('template'), $cache_id) ) {
                print 'from cache';
            }*/
        } else {
            // AJAX
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');

            if ( self::$request->getAjaxType() == Request::TYPE_JSON ) {
                print json_encode( array(
                    'error'     => self::$request->getError(),
                    'feedback'  => self::$request->getFeedback(),
                    'content'   => self::$request->getContent(),
                ));
            }
            else {
                print self::$request->getFeedbackString();
                if ( self::$request->getContent() ) {
                    print self::$request->getContent();
                }
            }
        }
    }

}
