<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Siteforever\Bundle\CmsBundle\EventListener;

use Sfcms\Kernel\KernelEvent;
use Sfcms\View\Layout;
use Sfcms\View\Xhr;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Stopwatch\Stopwatch;

class SfcmsKernelSubscriber implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvent::KERNEL_RESPONSE => array(
                array('prepareResult'),
                array('invokeLayout'),
            ),
        );
    }

    protected function getTpl()
    {
        return $this->container->get('tpl');
    }

    protected function getAuth()
    {
        return $this->container->get('auth');
    }

    protected function getLogger()
    {
        return $this->container->get('logger');
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
        if (is_array($result) && 'json' == $format) {
            // Если надо вернуть JSON из массива
            $result = defined('JSON_UNESCAPED_UNICODE')
                ? json_encode($result, JSON_UNESCAPED_UNICODE)
                : json_encode($result);
        }
        // Имеет больший приоритет, чем данные в Request-Request->content
        if (is_array($result) && ('html' == $format || null === $format)) {
            // Если надо отпарсить шаблон с данными из массива
            $this->getTpl()->assign($result);
            $template = $request->getController() . '.' . $request->getAction();
            $this->getTpl()->assign(array(
                    'request'   => $request,
                    'auth'      => $this->getAuth(),
                ));
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
            $Layout = new Xhr($this->container->get('kernel'), $this->container->getParameter('template'));
        } else {
            $Layout = new Layout($this->container->get('kernel'), $this->container->getParameter('template'));
        }
        $Layout->view($event);
        $this->getLogger()->info(sprintf('Invoke layout: %.3f sec', $watch->stop(__FUNCTION__)->getDuration() / 1000));
        return $event;
    }

    public function createSignature(KernelEvent $event)
    {
        if (!$this->container->hasParameter('silent')) {
            $event->getResponse()->headers->set('X-Powered-By', 'SiteForeverCMS');
        }
    }
}
