<?php
namespace Module\System\Subscriber;

use Module\System\Object\Log;
use Module\User\Object\User;
use Sfcms\Kernel\KernelEvent;
use Sfcms\Model\ModelEvent;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @author: keltanas <keltanas@gmail.com>
 */
class KernelSubscriber implements EventSubscriberInterface
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
        return [
            KernelEvent::KERNEL_RESPONSE => [
                ['onKernelResponse', 1000],
                ['onKernelResponseImage', 1000],
            ],
            'save.start' => 'onAllSaveStart',
        ];
    }

    /**
     * Handling the response
     * @param KernelEvent $event
     */
    public function onKernelResponse(KernelEvent $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof JsonResponse && Response::HTTP_FORBIDDEN == $response->getStatusCode()) {
            if (!$this->container->get('auth')->isLogged()) {
                $response = new RedirectResponse($this->container->get('router')->createLink('user/login'));
                $event->setResponse($response);
                $event->stopPropagation();
            }
        }
        if (!$response instanceof JsonResponse && Response::HTTP_NOT_FOUND == $response->getStatusCode()) {
            $this->container->get('tpl')->assign('request', $event->getRequest());
            $this->container->get('tpl')->assign('response', $event->getResponse());
            $response->setContent($this->container->get('tpl')->fetch('error.404'));
        }
    }

    /**
     * If result is image... This needing for captcha
     * @param KernelEvent $event
     */
    public function onKernelResponseImage(KernelEvent $event)
    {
        if (is_resource($event->getResult()) && imageistruecolor($event->getResult())) {
            $event->getResponse()->headers->set('Content-type', 'image/png');
            imagepng($event->getResult());
            $event->stopPropagation();
        }
    }

    /**
     * Логировать сохранение объектов
     * @param $event
     */
    public function onAllSaveStart(ModelEvent $event)
    {
        $model = $this->container->get('data.manager')->getModel('Log');
        // Записываем все события в таблицу log
        $config = $this->container->getParameter('database');
        if (isset($config['log']) && $config['log']) {
            $obj = $event->getObject();
            if ($obj instanceof User || $obj instanceof Log) {
                return;
            }
            /** @var $log Log */
            $log = $model->createObject();
            $log->markNew();
            $log->user = $this->container->get('auth')->getId() ?: 0;
            $log->object = get_class($obj);
            $log->object_id = $obj->getId();
            $log->action = 'save';
            $log->timestamp = time();
        }
    }
}
