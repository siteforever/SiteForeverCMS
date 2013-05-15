<?php
set_error_handler( function ( $errno, $errstr) {
    throw new Exception( $errstr, $errno );
}, E_WARNING & E_NOTICE & E_DEPRECATED & E_USER_WARNING & E_USER_NOTICE & E_ERROR );

use Sfcms\Kernel\KernelBase;
use Sfcms\Kernel\KernelEvent;
use Sfcms\Model;
use Sfcms\Request;
use Symfony\Component\HttpFoundation\Response;
use Sfcms\Controller\Resolver;
use Sfcms\Data\Watcher;
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
class App extends KernelBase
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
        $response = $this->handleRequest();
        $this->flushDebug();
        $response->prepare($this->getRequest());
        $response->send();
//        print round(microtime(1) - self::$start_time, 3);
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
        $this->getConfig()->setDefault('language', 'en');
        if (!$this->getRequest()->get('lang')) {
            $this->getRequest()->set('lang', $this->getConfig('language'));
        } else {
            $this->getConfig()->set('language', $this->getRequest()->get('lang'));
        }
        i18n::getInstance()->setLanguage(
            $this->getConfig()->get('language')
        );
        $this->getRequest()->setLocale($this->getConfig()->get('language'));

        // TIME_ZONE
        date_default_timezone_set($this->getConfig('timezone') ? : 'Europe/Moscow');

        if (!defined('MAX_FILE_SIZE')) {
            define('MAX_FILE_SIZE', 2 * 1024 * 1024);
        }
        $installer = new Sfcms_Installer();
        $installer->installationStatic();

        $this->getEventDispatcher()->addListener('kernel.response', array($this, 'prepareResult'));
        $this->getEventDispatcher()->addListener('kernel.response', array($this, 'prepareReload'));
        $this->getEventDispatcher()->addListener('kernel.response', array($this, 'invokeLayout'));
        $this->getEventDispatcher()->addListener('kernel.response', array($this, 'createSignature'));
    }


    /**
     * Обработка запросов
     * @static
     * @return Response
     */
    protected function handleRequest()
    {
        // запуск сессии
        $this->getSession();
        // маршрутизатор
        $this->getRouter()->routing();

        self::$init_time = microtime( 1 ) - self::$start_time;
        self::$controller_time = microtime( 1 );

        $result = null;
        $response = null;
        try {
            $result = $this->getResolver()->dispatch($this->getRequest());
        } catch ( Sfcms_Http_Exception $e ) {
            $response = new Response($e->getMessage(), $e->getCode()?:500);
            if (403 == $e->getCode()) {
                $response->headers->set('Location', $this->getRouter()->createServiceLink('user','login'));
                return $response;
            }
        } catch ( Exception $e ) {
            $this->getLogger()->log($e->getMessage() . ' IN FILE ' . $e->getFile() . ':' . $e->getLine());
            $this->getLogger()->log($e->getTraceAsString());
            return new Response($e->getMessage() . (static::isDebug() ? '<pre>' . $e->getTraceAsString() : ''), 500);
        }

        if (! $response && is_string($result)) {
            $response = new Response($result);
        } elseif ($result instanceof Response) {
            $response = $result;
        } elseif (!$response) {
            $response = new Response();
        }

        // If result is image... This needing for captcha
        if (is_resource($result) && imageistruecolor($result)) {
            $response->headers->set('Content-type', 'image/png');
            imagepng( $result );
            return $response;
        }

        self::$controller_time = microtime( 1 ) - self::$controller_time;

        $event = new KernelEvent($response, $this->getRequest(), $result);
        $this->getEventDispatcher()->dispatch('kernel.response', $event);

        // Выполнение операций по обработке объектов
        try {
            Watcher::instance()->performOperations();
        } catch ( ModelException $e ) {
            $response->setStatusCode(500);
            $response->setContent($e->getMessage());
        }

        return $event->getResponse();
    }

    /**
     * Если контроллер вернул массив, то преобразует его в строку и сохранит в Response
     * @param KernelEvent $event
     * @return string
     */
    public function prepareResult(KernelEvent $event)
    {
        $result = $event->getResult();
        $response = $event->getResponse();
        $format = $this->getRequest()->getRequestFormat();
        if (is_array($result) && 'json' == $format) {
            // Если надо вернуть JSON из массива
            $result = json_encode($result);
        }
        // Имеет больший приоритет, чем данные в Request-Request->content
        if (is_array($result) && ('html' == $format || null === $format)) {
            // Если надо отпарсить шаблон с данными из массива
            $this->getTpl()->assign($result);
            $template = $this->getRequest()->getController() . '.' . $this->getRequest()->getAction();
            $result   = $this->getTpl()->fetch($template);
        }
        // Просто установить итоговую строку как контент
        if (is_string($result)) {
            $response->setContent($result);
        }
        return $event;
    }

    /**
     * Перезагрузка страницы
     * @param KernelEvent $event
     *
     * @return KernelEvent
     */
    public function prepareReload(KernelEvent $event)
    {
        if ( $reload = $this->getRequest()->get('reload') ) {
            $event->getResponse()->setContent($event->getResponse()->getContent() . $reload);
        }
        return $event;
    }

    /**
     * Вызвать отображение
     * @param KernelEvent $event
     *
     * @return KernelEvent
     */
    public function invokeLayout(KernelEvent $event)
    {
        if( $this->getRequest()->getAjax() ) {
            $Layout = new Xhr($this);
        } else {
            $Layout = new Layout($this);
        }
        return $Layout->view($event);
    }

    public function createSignature(Sfcms\Kernel\KernelEvent $event)
    {
        if (!$this->getConfig('silent')) {
            $event->getResponse()->headers->set('X-Powered-By', 'SiteForeverCMS');
        }
    }


    /**
     * Flushing debug info
     */
    protected function flushDebug()
    {
        // todo Вывод в консоль FirePHP вызывает исключение, если не включена буферизация вывода
        // Fatal error: Exception thrown without a stack frame in Unknown on line 0

        if ( App::isDebug() ) {
            if ( $this->getConfig()->get( 'db.debug' ) ) {
                Model::getDB()->saveLog();
                $this->getLogger()->log(
                    "Total SQL: " . count( Model::getDB()->getLog() )
                        . "; time: " . round( Model::getDB()->time, 3 ) . " sec.", 'app'
                );
            }
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
