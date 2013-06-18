<?php
// user groups
define('USER_GUEST', '0'); // гость
define('USER_USER',  '1'); // юзер
define('USER_WHOLE', '2'); // оптовый покупатель
define('USER_ADMIN', '10'); // админ


use Sfcms\Kernel\KernelBase;
use Sfcms\Kernel\KernelEvent;
use Sfcms\Model;
use Sfcms\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sfcms\Controller\Resolver;
use Sfcms\Data\Watcher;
use Sfcms\View\Layout;
use Sfcms\View\Xhr;
use Sfcms\i18n;
use Sfcms\Model\Exception as ModelException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        static::$start_time = microtime( true );
        $this->init();
        $request  = Request::createFromGlobals();
        $response = $this->handleRequest($request);
        $this->flushDebug();
        $response->prepare($request);
        $response->send();
//        print round(microtime(1) - static::$start_time, 3);
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
     * @return void
     */
    public function init()
    {
        // Language
        $this->getConfig()->setDefault('language', 'en');

        // TIME_ZONE
        date_default_timezone_set($this->getConfig('timezone') ? : 'Europe/Moscow');

        if (!defined('MAX_FILE_SIZE')) {
            define('MAX_FILE_SIZE', 2 * 1024 * 1024);
        }
        $installer = new Sfcms_Installer();
        $installer->installationStatic();

        $this->getEventDispatcher()->addListener('kernel.response', function(KernelEvent $event){
                if ($event->getResponse() instanceof RedirectResponse) {
                    $event->stopPropagation();
                }
            });
        $this->getEventDispatcher()->addListener('kernel.response', array($this, 'prepareResult'));
        $this->getEventDispatcher()->addListener('kernel.response', array($this, 'prepareReload'));
        $this->getEventDispatcher()->addListener('kernel.response', array($this, 'invokeLayout'));
        $this->getEventDispatcher()->addListener('kernel.response', array($this, 'createSignature'));
    }


    /**
     * Обработка запросов
     * @param Request $request
     *
     * @return Response
     */
    public function handleRequest(Request $request)
    {
        $acceptableContentTypes = $request->getAcceptableContentTypes();
        $format = null;
        if ($acceptableContentTypes) {
            $format = $request->getFormat($acceptableContentTypes[0]);
        }
        $request->setRequestFormat($format);
        $request->setDefaultLocale($this->getConfig('language'));
        i18n::getInstance()->setLanguage($request->getLocale());

        // запуск сессии
        if (!$request->getSession()) {
            $sessionHelper = new \Sfcms\Session($this->getModel('Session'));
            $request->setSession($sessionHelper->session());
            $request->getSession()->start();
        }
        // маршрутизатор
        $router = new \Sfcms\Router($request);
        $router->setRewrite($this->getConfig('url.rewrite'));
        $this->setRouter($router);
        $router->routing();

        $this->setAuth(new \Auth($request));

        static::$init_time = microtime( 1 ) - static::$start_time;
        static::$controller_time = microtime( 1 );

        $result = null;
        /** @var Response $response */
        $response = null;
        try {
            $result = $this->getResolver()->dispatch($request);
        } catch (HttpException $e) {
            $this->getLogger()->log($e->getMessage());
            $response = new Response($e->getMessage(), $e->getStatusCode()?:500);
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

        static::$controller_time = microtime( 1 ) - static::$controller_time;

        $event = new KernelEvent($response, $request, $result);
        $this->getEventDispatcher()->dispatch('kernel.response', $event);

        // Выполнение операций по обработке объектов
        try {
            Watcher::instance()->performOperations();
        } catch (ModelException $e) {
            $response->setStatusCode(500);
            $response->setContent($e->getMessage());
        } catch (PDOException $e) {
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
        $request = $event->getRequest();
        $format = $request->getRequestFormat();
        if (is_array($result) && 'json' == $format) {
            // Если надо вернуть JSON из массива
            $result = json_encode($result);
        }
        // Имеет больший приоритет, чем данные в Request-Request->content
        if (is_array($result) && ('html' == $format || null === $format)) {
            // Если надо отпарсить шаблон с данными из массива
            $this->getTpl()->assign($result);
            $template = $request->getController() . '.' . $request->getAction();
            $result   = $this->getTpl()->fetch(strtolower($template));
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
        if ( $reload = $event->getRequest()->get('reload') ) {
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
        if ($event->getRequest()->getAjax()) {
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

        if ( self::isDebug() ) {
            if ( $this->getConfig()->get( 'db.debug' ) ) {
                Model::getDB()->saveLog();
                $this->getLogger()->log(
                    "Total SQL: " . count( Model::getDB()->getLog() )
                        . "; time: " . round( Model::getDB()->time, 3 ) . " sec.", 'app'
                );
            }
            $this->getLogger()->log( "Init time: " . round( static::$init_time, 3 ) . " sec.", 'app' );
            $this->getLogger()->log( "Controller time: " . round( static::$controller_time, 3 ) . " sec.", 'app' );
            $exec_time = microtime( true ) - static::$start_time;
            $this->getLogger()->log(
                "Other time: " . round( $exec_time - static::$init_time - static::$controller_time, 3 ) . " sec.", 'app'
            );
            $this->getLogger()->log( "Execution time: " . round( $exec_time, 3 ) . " sec.", 'app' );
            $this->getLogger()->log( "Required memory: " . round( memory_get_peak_usage(true) / 1024, 3 ) . " kb.", 'app' );
        }
    }
}
