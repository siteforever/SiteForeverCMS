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
            'User'          => array( self::BELONGS, 'User', 'user_id' ),
            'Delivery'      => array( self::BELONGS, 'Delivery', 'delivery_id' ),
            'Payment'       => array( self::BELONGS, 'Payment', 'payment_id' ),
            'Count'         => array( self::STAT,     'OrderPosition', 'ord_id' ),
            'Positions'     => array( self::HAS_MANY, 'OrderPosition', 'ord_id' ),
            'Status'        => array( self::BELONGS,  'OrderStatus', 'status' ),
        );
    }


    /**
     * Создать заказ
     * @param Basket $basket
     * @param Delivery $delivery
     * @return bool|Order
     */
    public function createOrder( Basket $basket, Forms_Basket_Address $form, Sfcms\Delivery $delivery )
    {
        $basketData = $basket->getAll();

        /** @var $obj Order */
        $obj    = $this->createObject();
        $obj->attributes = $form->getData();

        $metro = ! $form->metro ? null : $this->getModel('Metro')->find($form->metro);
        $obj->address   = join(', ', array_filter(array(
            $form->zip,
            $form->country,
            $form->city,
            null === $metro ? false : t('subway') . ' ' . $metro->name,
            $form->address,
        )));
        $obj->status    = 1;
        $obj->paid      = 0;
        $obj->date      = time();
        $obj->user_id   = $this->app()->getAuth()->currentUser()->getId();
        $obj->delivery  = 0;
        if ( $delivery->getType() ){
            $obj->delivery = $delivery->getType();
        }

        $this->save( $obj );

//        $this->log( $basketData, 'basketData' );


        // Заполняем заказ товарами

        /** @var $orderPositionModel OrderPositionModel */
        $orderPositionModel = $this->getModel('OrderPosition');

        if ( $obj->getId() ) {
            $pos_list    = array();
            $total_count = 0;
            $total_summa = 0;
            foreach( $basketData as $data ) {
                /** @var $position OrderPosition */
                $position   = $orderPositionModel->createObject();
                $position->attributes = array(
                    'ord_id'    => $obj->getId(),
//                    'name'      => $data['name'],
                    'product_id'=> (int) $data['id'],
                    'articul'   => ! empty( $data['articul'] ) ? $data['articul'] : $data['name'],
                    'details'   => $data['details'],
                    'currency'  => isset( $data['currency'] ) ? $data['currency'] : t('catalog','RUR'),
                    'item'      => isset( $data['item'] ) ? $data['item'] : t('catalog', 'item'),
                    'cat_id'    => is_numeric( $data['id'] ) ? $data['id'] : '0',
                    'price'     => $data['price'],
                    'count'     => $data['count'],
                    'status'    => 1,
                );
                $position->save();

                $pos_list[] = $position->attributes;
            }
            $total_count = $basket->getCount();
            $total_summa = $basket->getSum() + $delivery->cost();

            $this->app()->getTpl()->assign(array(
                'order'     => $obj,
                'sitename'  => $this->config->get('sitename'),
                'ord_link'  => $this->app()->getConfig()->get('siteurl').$obj->getUrl(),
                'user'      => $this->app()->getAuth()->currentUser()->getAttributes(),
                'date'      => date('H:i d.m.Y'),
                'order_n'   => $obj->getId(),
                'positions' => $pos_list,
                'total_summa'=> $total_summa,
                'total_count'=> $total_count,
                'delivery'  => $delivery,
                'sum'       => $basket->getSum(),
            ));

            sendmail(
                $obj->email,
                $this->config->get('admin'),
                sprintf('Новый заказ с сайта %s №%s',$this->config->get('sitename'),$obj->getId()),
                $this->app()->getTpl()->fetch('order.mail.createadmin')
            );

            sendmail(
                $this->config->get('sitename').' <'.$this->config->get('admin').'>',
                $obj->email,
                sprintf('Заказ №%s на сайте %s',$obj->getId(),$this->config->get('sitename')),
                $this->app()->getTpl()->fetch('order.mail.create')
            );

            return $obj;
        }
        return false;
    }
}
