<?php
/**
 * Доставка
 * @author keltanas
 */
namespace Sfcms;

use Module\Market\Event\Event;
use Module\Market\Event\DeliveryEvent;
use Module\Market\Object\Order;
use Sfcms\Basket\Base as Basket;
use Module\Market\Object\Delivery as DeliveryObject;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DeliveryManager
{
    protected $id;

    /** @var Request */
    protected $request;

    /** @var Order */
    protected $order;

    /** @var DeliveryObject */
    protected $delivery = null;

    /** @var EventDispatcher  */
    protected $eventDispatcher;

    /**
     * @param Request $request
     * @param Order $order
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(Request $request, Order $order, EventDispatcher $eventDispatcher)
    {
        $this->request = $request;
        $this->order = $order;
        $this->id = $this->request->getSession()->get('delivery');
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     *
     * @return DeliveryObject|null
     * @throws Exception
     */
    public function getObject($id = null)
    {
        if (null === $this->delivery) {
            $model = \App::cms()->getDataManager()->getModel('Delivery');
            if (null !== $id) {
                $this->delivery = $model->find($id);
            } elseif (null !== $this->id) {
                $this->delivery = $model->find($this->id);
            }
            return null;
        }

        return $this->delivery;
    }


    /**
     * Стоимость доставки
     *
     * @return float
     */
    public function cost()
    {
        if ($this->getObject()) {
            $event = new DeliveryEvent($this->getObject(), $this->order);
            $this->eventDispatcher->dispatch(Event::DELIVERY_COST_CALCULATE, $event);
            return $event->getCost();
        }

        return 0;
    }


    public function getType()
    {
        return $this->id;
    }


    public function setType($type)
    {
        if ($this->id) {
            $this->getObject($type);
        }
        $this->id = $type;
        $this->request->getSession()->set('delivery', $this->id);
    }
}
