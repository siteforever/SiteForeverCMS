<?php
namespace Module\Market\Event;

use Module\Market\Object\Order;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event is called when something happens with the order
 * @author: keltanas <keltanas@gmail.com>
 */
class OrderEvent extends Event
{
    /** @var \Module\Market\Object\Order  */
    protected $order;

    public function __construct(Order $order)
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
