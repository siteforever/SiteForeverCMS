<?php
/**
 * Event dispatched for order payment
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Module\Market\Event;

use Module\Market\Component\Payment;
use Module\Market\Object\Payment as PaymentType;
use Module\Market\Object\Order;
use Sfcms\DeliveryManager;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class PaymentEvent extends SymfonyEvent
{
    /** @var Payment */
    private $payment = null;

    /** @var Order */
    private $order;

    /** @var PaymentType */
    private $paymentType;

    /** @var DeliveryManager */
    private $deliveryManager;

    public function __construct(DeliveryManager $deliveryManager, Order $order)
    {
        $this->deliveryManager = $deliveryManager;
        $this->order = $order;
        $this->paymentType = $order->Payment;
    }


    /**
     * @param \Module\Market\Component\Payment $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return \Module\Market\Component\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param \Sfcms\DeliveryManager $deliveryManager
     */
    public function setDeliveryManager(DeliveryManager $deliveryManager)
    {
        $this->deliveryManager = $deliveryManager;
    }

    /**
     * @return \Sfcms\DeliveryManager
     */
    public function getDeliveryManager()
    {
        return $this->deliveryManager;
    }

    /**
     * @param \Module\Market\Object\Payment $paymentType
     */
    public function setPaymentType(PaymentType $paymentType)
    {
        $this->paymentType = $paymentType;
    }

    /**
     * @return \Module\Market\Object\Payment
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @param \Module\Market\Object\Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return \Module\Market\Object\Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}
