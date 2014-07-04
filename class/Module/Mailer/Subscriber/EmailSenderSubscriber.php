<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Mailer\Subscriber;


use Sfcms\Kernel\KernelEvent;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailSenderSubscriber extends ContainerAware implements EventSubscriberInterface
{
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
            KernelEvent::KERNEL_TERMINATE => 'onKernelResponse',
        );
    }

    public function onKernelResponse(KernelEvent $event)
    {
        if (!$this->container->has('mailer')) {
            return;
        }

        if ($this->container->getParameter('mailer.spool')) {
            /** @var \Swift_Mailer $mailer */
            $mailer = $this->container->get('mailer');
            $transport = $mailer->getTransport();
            if ($transport instanceof \Swift_Transport_SpoolTransport) {
                $spool = $transport->getSpool();
                if ($spool instanceof \Swift_MemorySpool) {
                    $spool->flushQueue($this->container->get('mailer_transport_real'));
                }
            }
        }
    }
}
