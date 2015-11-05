<?php
/**
 * Event for delivery costs
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Market\Event;


use Module\Market\Object\Delivery;
use Module\Market\Object\Order;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

class DeliveryEvent extends BaseEvent
{
    /** @var Delivery */
    protected $delivery;

    /** @var Order */
    protected $order;

    protected $cost;

    public function __construct(Delivery $delivery, Order $order)
    {
        $this->delivery = $delivery;
        $this->order = $order;
        $this->cost = $delivery->cost;
    }

    /**
     * @param mixed $cost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return \Module\Market\Object\Delivery
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @return \Module\Market\Object\Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}
