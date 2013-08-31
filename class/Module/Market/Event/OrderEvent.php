<?php
namespace Module\Market\Event;

use Module\Market\Object\Order;
use Sfcms\Basket\Base as Basket;
use Sfcms\Delivery;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event is called when something happens with the order
 * @author: keltanas <keltanas@gmail.com>
 */
class OrderEvent extends Event
{
    /** @var Order  */
    protected $order;

    /** @var Basket */
    protected $basket;

    /** @var Delivery */
    protected $delivery;

    public function __construct(Order $order, Basket $basket = null, Delivery $delivery = null)
    {
        $this->order = $order;
        $this->basket = $basket;
        $this->delivery = $delivery;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return Basket
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * @return Delivery
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

}
