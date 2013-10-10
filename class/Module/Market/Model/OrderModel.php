<?php
/**
 * Модель заказа
 * @author Nikolay Ermin
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Module\Market\Model;

use Module\Market\Form\OrderForm;
use Sfcms;
use Sfcms\Model;
use Sfcms\Basket\Base as Basket;
use Module\Market\Object\Delivery;
use Module\Market\Object\Order;
use Module\Market\Object\OrderPosition;

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
     * @param Model\ModelEvent $event
     */
    public function onSaveSuccess(Model\ModelEvent $event)
    {
        array_map(function(OrderPosition $pos) use ($event) {
            $pos->ord_id = $event->getObject()->id;
            $pos->save();
        }, iterator_to_array($event->getObject()->Positions));
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
}
