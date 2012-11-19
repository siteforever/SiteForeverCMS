<?php
/**
 * Доставка
 * @author keltanas
 */
namespace Sfcms;

use App;
use Basket;
use Data_Object_Delivery;

class Delivery
{
    protected $id;

    /** @var Session */
    protected $session;

    /** @var Basket */
    protected $basket;

    /** @var Data_Object_Delivery */
    protected $delivery = null;


    /**
     * @param Session $session
     */
    public function __construct( Session $session, Basket $basket )
    {
        $this->basket  = $basket;
        $this->session = $session;
        $this->id = $this->session->get('delivery');
    }


    /**
     * @return \Data_Object_Delivery|null
     */
    public function getObject( $id = null )
    {
        if ( null === $this->delivery) {
            $model = \Sfcms_Model::getModel('Delivery');
            if ( null !== $id ) {
                $this->delivery = $model->find( $id );
            } elseif ( null !== $this->id ) {
                $this->delivery = $model->find( $this->id );
            }
            if ( null === $this->delivery ) {
                throw new Exception('Delivery not found', 1);
            }
        }
        return $this->delivery;
    }


    /**
     * Стоимость доставки
     * @param float $sum
     * @return float
     */
    public function cost( $sum = null )
    {
        try {
            $obj = $this->getObject();
        } catch ( Exception $e ) {
            return null;
        }
        if ( null === $sum && $this->basket->getSum() ) {
            $sum = $this->basket->getSum();
        }
        if ( $sum >= 4000 ) {
            return $obj->cost <= 400 ? 0 : $obj->cost - 400;
        }
        return $obj->cost;
    }


    public function getType()
    {
        return $this->id;
    }


    public function setType( $type )
    {
        if ( $this->id ) {
            $this->getObject( $type );
        }
        $this->id = $type;
        $this->session->set('delivery', $this->id);
    }
}
