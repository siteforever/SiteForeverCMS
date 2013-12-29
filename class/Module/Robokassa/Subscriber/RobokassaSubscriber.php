<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Robokassa\Subscriber;


use Module\Market\Event\Event;
use Module\Market\Event\PaymentEvent;
use Module\Robokassa\Component\Robokassa;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RobokassaSubscriber implements EventSubscriberInterface
{
    /** @var Robokassa */
    private $robokassa;

    /** @var string */
    private $sitename;

    public function __construct(Robokassa $robokassa, $sitename)
    {
        $this->robokassa = $robokassa;
    }

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
            Event::ORDER_PAYMENT => array(
                array('onPayment', 0),
            )
        );
    }

    public function onPayment(PaymentEvent $event)
    {
        $order = $event->getOrder();
        $this->robokassa->setInvId($order->getId());
        $this->robokassa->setOutSum($order->getSum() + $event->getDeliveryManager()->cost($order->getSum()));
        $this->robokassa->setDesc(
            sprintf(
                'Оплата заказа №%s в интернет-магазине %s',
                $order->getId(),
                $this->sitename
            )
        );

        return $event->setPayment($this->robokassa);
    }

}
