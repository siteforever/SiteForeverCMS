<?php
namespace Module\Market\Event;

use Module\Market\Object\Order;
use Sfcms\Basket\Base as Basket;
use Sfcms\DeliveryManager;
use Sfcms\Request;
use Sfcms\Controller;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

/**
 * Event is called when something happens with the order
 * @author: keltanas <keltanas@gmail.com>
 */
class OrderEvent extends BaseEvent
{
    /** @var Order  */
    protected $order;

    /** @var Basket */
    protected $basket;

    /** @var DeliveryManager */
    protected $delivery;

    /** @var Request */
    protected $request;

    /** @var Controller */
    protected $controller;

    protected $result = array();

    public function __construct(Order $order, Request $request, Controller $controller, Basket $basket = null, DeliveryManager $delivery = null)
    {
        $this->order = $order;
        $this->request = $request;
        $this->controller = $controller;
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
     * @return DeliveryManager
     */
    public function getDeliveryManager()
    {
        return $this->delivery;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }
}
