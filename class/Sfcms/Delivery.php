<?php
/**
 * Доставка
 * @author keltanas
 */
namespace Sfcms;

use App;
use Sfcms\Basket\Base as Basket;
use Module\Market\Object\Delivery as DeliveryObject;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Delivery
{
    protected $id;

    /** @var SessionInterface */
    protected $session;

    /** @var Basket */
    protected $basket;

    /** @var DeliveryObject */
    protected $delivery = null;

    protected $bound = 0;


    /**
     * @param SessionInterface $session
     * @param Basket  $basket
     */
    public function __construct(SessionInterface $session, Basket $basket, $bound = 0)
    {
        $this->basket  = $basket;
        $this->session = $session;
        $this->id      = $this->session->get('delivery');
        $this->bound   = $bound;
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
            $model = Model::getModel('Delivery');
            if (null !== $id) {
                $this->delivery = $model->find($id);
            } elseif (null !== $this->id) {
                $this->delivery = $model->find($this->id);
            }
            if (null === $this->delivery) {
                throw new Exception('Delivery not found', 1);
            }
        }

        return $this->delivery;
    }


    /**
     * Стоимость доставки
     * @param float $sum
     *
     * @return float
     */
    public function cost($sum = null)
    {
        try {
            $obj = $this->getObject();
        } catch (Exception $e) {
            return null;
        }

        if ($this->bound) {
            if (null === $sum && $this->basket->getSum()) {
                $sum = $this->basket->getSum();
            }
            if ($sum >= $this->bound) {
                return $obj->cost <= round($this->bound / 10) ? 0 : $obj->cost - round($this->bound / 10);
            }
        }

        return $obj->cost;
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
        $this->session->set('delivery', $this->id);
    }
}
