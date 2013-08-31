<?php
/**
 * Модель заказа
 * @author Nikolay Ermin
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Module\Market\Model;

use Sfcms;
use Sfcms\Model;
use Sfcms\Basket\Base as Basket;
use Module\Market\Object\Delivery;
use Module\Market\Object\Order;
use Module\Market\Object\OrderPosition;
use Forms_Basket_Address;

class OrderModel extends Model
{
    protected $positions = array();

    protected $statuses;

    /** @var OrderPositionModel */
    public  $model_position;

    /**
     * Инициализация
     * @return void
     */
    protected function init()
    {
        $this->model_position   = $this->getModel('OrderPosition');
    }

    /**
     * Отношения
     * @return array
     */
    public function relation()
    {
        return array(
            'User' => array(self::BELONGS, 'User', 'user_id'),
            'Delivery' => array(self::BELONGS, 'Delivery', 'delivery_id'),
            'Payment' => array(self::BELONGS, 'Payment', 'payment_id'),
            'Count' => array(self::STAT, 'OrderPosition', 'ord_id'),
            'Positions' => array(self::HAS_MANY, 'OrderPosition', 'ord_id', 'with'=>array('Product')),
            'Status' => array(self::BELONGS, 'OrderStatus', 'status'),
        );
    }


    /**
     * Create order
     * @param Forms_Basket_Address $form
     * @param Sfcms\Delivery $delivery
     * @return Order|null
     */
    public function createOrder(Forms_Basket_Address $form, Sfcms\Delivery $delivery)
    {
        /** @var $obj Order */
        $obj = $this->createObject();
        $obj->attributes = $form->getData();

        $metro = !$form->metro ? null : $this->getModel('Metro')->find($form->metro);
        $obj->address = join(', ',
            array_filter(array(
                    $form->zip,
                    $form->country,
                    $form->city,
                    null === $metro ? false : $this->t('subway') . ' ' . $metro->name,
                    $form->address,
                )
            )
        );

        $obj->status = 1;
        $obj->paid = 0;
        $obj->date = time();
        $obj->user_id = $this->app()->getAuth()->getId();
        $obj->delivery = 0;

        if ($delivery->getType()) {
            $obj->delivery = $delivery->getType();
        }

        $this->save($obj);

        if ($obj->getId()) {
            return $obj;
        }

        return null;
    }
}
