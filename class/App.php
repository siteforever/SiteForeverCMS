<?php
// user groups
define('USER_ANONIMUS', null); // аноним
define('USER_GUEST', '0'); // гость
define('USER_USER',  '1'); // юзер
define('USER_WHOLE', '2'); // оптовый покупатель
define('USER_ADMIN', '10'); // админ

if (!defined('SF_PATH')) {
    define('SF_PATH', realpath(__DIR__ . '/..'));
}

use Sfcms\Kernel\AbstractKernel;
use Sfcms\Kernel\KernelEvent;
use Sfcms\Model;
use Sfcms\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sfcms\Data\Watcher;
use Sfcms\View\Layout;
use Sfcms\View\Xhr;
use Sfcms\Model\Exception as ModelException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sfcms\Form\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Класс приложение
 * FrontController
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://siteforever.ru
 */
class App extends AbstractKernel
{
    /**
     * Run application
     * @param $request
     */
    public function run(Request $request = null)
    {
        static::$start_time = microtime(true);

        if (null === $request) {
            Request::enableHttpMethodParameterOverride();
            $request  = Request::createFromGlobals();
        }

        date_default_timezone_set($this->getContainer()->hasParameter('timezone')
                ? $this->getContainer()->getParameter('timezone') : 'Europe/Moscow');

        $response = $this->handleRequest($request);

        $this->flushDebug();
        $response->prepare($request);
        $response->send();
        $this->getEventDispatcher()->dispatch(KernelEvent::KERNEL_TERMINATE, new KernelEvent($response, $request));
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
     * @return string
     */
    public function getContainerCacheFile()
    {
        return $this->getCachePath() . sprintf('/container_%s.php', $this->getEnvironment());
    }

    /**
     * @return string
     */
    public function getLogsPath()
    {
        return ROOT . '/var/logs';
    }

    /**
     * @return string
     */
    public function getCachePath()
    {
        return ROOT . '/var/cache/' . $this->getEnvironment();
    }

    public function redirectListener(KernelEvent $event)
    {
        if ($event->getResponse() instanceof RedirectResponse) {
            $event->stopPropagation();
        }
    }

    /**
     * Handle request
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    public function handleRequest(Request $request = null)
    {
        $this->getLogger()->log(str_repeat('-', 80));
        $this->getLogger()->log(sprintf('---%\'--74s---', $request->getRequestUri()));
        $this->getLogger()->log(str_repeat('-', 80));

        $this->getContainer()->set('request', $request);
        $this->getAuth()->setRequest($request);
        $acceptableContentTypes = $request->getAcceptableContentTypes();
        $format = null;
        if ($acceptableContentTypes) {
            $format = $request->getFormat($acceptableContentTypes[0]);
        }
        $request->setRequestFormat($format);
        $request->setDefaultLocale($this->getContainer()->getParameter('language'));

        static::$init_time = microtime(1) - static::$start_time;
        static::$controller_time = microtime(1);

        $result = null;
        /** @var Response $response */
        $response = null;
        try {
            $container = $this->getContainer();
            $tpl = $this->getTpl();
            $tpl->assign([
                    'sitename' => $container->getParameter('sitename'),
                    'debug' => $container->getParameter('debug'),
                ]);
//            $tpl->assign($this->getContainer()->getParameterBag()->all());
            $this->getRouter()->setRequest($request)->routing();
            $result = $this->getResolver()->dispatch($request);
        } catch (HttpException $e) {
            $this->getLogger()->error($e->getMessage());
            $errors = [];
            if ($e instanceof ValidationException) {
                $errors = $e->getErrors();
            }
            if ('json' == $request->getContentType() ||
                'json' == $request->getFormat($request->headers->get('ACCEPT'))
            ) {
                $response = new JsonResponse(
                    ['error' => 1, 'msg' => $e->getMessage(), 'errors' => $errors],
                    $e->getStatusCode() ?: JsonResponse::HTTP_INTERNAL_SERVER_ERROR
                );
            } else {
                $response = new Response(
                    $e->getMessage(),
                    $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage() . ' IN FILE ' . $e->getFile() . ':' . $e->getLine(), $e->getTrace());
            if ($this->isDebug()) {
                throw $e;
            } else {
                switch ($request->getContentType()) {
                    case 'json':
                        return new JsonResponse(array('error'=>1, 'msg'=>'Site error'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
                }
                return new Response('Site error', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        if (!$response && is_string($result)) {
            $response = new Response($result);
        } elseif ($result instanceof Response) {
            $response = $result;
        } elseif (!$response) {
            $response = new Response();
        }

        static::$controller_time = microtime(1) - static::$controller_time;

        $event = new KernelEvent($response, $request, $result);
        $this->getEventDispatcher()->dispatch(KernelEvent::KERNEL_RESPONSE, $event);

        // Выполнение операций по обработке объектов
        try {
            Watcher::instance()->performOperations();
        } catch (ModelException $e) {
            $this->getLogger()->error($e->getMessage(), $e->getTrace());
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent($e->getMessage());
        } catch (PDOException $e) {
            $this->getLogger()->error($e->getMessage(), $e->getTrace());
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
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
        $response = $event->getResponse();
        $result = $event->getResult();
        $request = $event->getRequest();
        $format = $request->getRequestFormat();
        // Имеет больший приоритет, чем данные в Request-Request->content
        if (is_array($result) && ('html' == $format || null === $format)) {
            // Если надо отпарсить шаблон с данными из массива
            $this->getTpl()->assign($result);
            $template = $request->getController() . '.' . $request->getAction();
            $this->getTpl()->assign('request', $request);
            $this->getTpl()->assign('response', $response);
            $this->getTpl()->assign('auth', $this->getAuth());
            $result   = $this->getTpl()->fetch(strtolower($template));
            $response->setContent($result);
        } elseif (is_array($result) && 'json' == $format) {
            // Если надо вернуть JSON из массива
            $event->setResponse(new JsonResponse($result, $response->getStatusCode(), $response->headers->all()));
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
        if ($reload = $event->getRequest()->get('reload')) {
            $event->getResponse()->setContent($event->getResponse()->getContent() . $reload);
        }
        return $event;
    }

    /**
     * Вызвать обертку для представления
     * @param KernelEvent $event
     *
     * @return KernelEvent
     */
    public function invokeLayout(KernelEvent $event)
    {
        $watch = (new Stopwatch())->start(__FUNCTION__);
        if ($event->getResponse() instanceof JsonResponse || $event->getRequest()->getAjax()) {
            $Layout = new Xhr($this, $this->getContainer()->getParameter('template'));
        } else {
            $Layout = new Layout($this, $this->getContainer()->getParameter('template'));
        }
        $Layout->view($event);

        $this->getLogger()->info(sprintf('Invoke layout: %.3f sec', $watch->stop(__FUNCTION__)->getDuration() / 1000));
        return $event;
    }

    public function createSignature(Sfcms\Kernel\KernelEvent $event)
    {
        if (!$this->getContainer()->hasParameter('silent')) {
            $event->getResponse()->headers->set('X-Powered-By', 'SiteForeverCMS');
        }
    }
}
