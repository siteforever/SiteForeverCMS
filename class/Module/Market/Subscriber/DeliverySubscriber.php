<?php
/**
 * Empty subscriber for example
 * @author: Nikolay Ermin <keltanas@gmail.com>
 *
 * Adding example for config.yml:
 *
 * services:
 *   my.delivery.subscriber:
 *   class: Keltanas\Market\Event\DeliverySubscriber
 *   tags:
 *     - { name: "delivery.subscriber" }
 *   calls:
 *     - [setBound, [5000]]
 */

namespace Module\Market\Subscriber;


use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeliverySubscriber extends ContainerAware implements EventSubscriberInterface
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
        );
    }
}
