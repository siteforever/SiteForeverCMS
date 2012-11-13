<?php
/**
 * Модель заказа
 * @author Nikolay Ermin 
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
class Model_Order extends Sfcms_Model
{
    protected $positions = array();

    protected $statuses;

    /** @var Model_OrderPosition */
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
     * @param Data_Object_Delivery $delivery
     * @return bool|Data_Object_Order
     */
    public function createOrder( Basket $basket, Forms_Basket_Address $form, Sfcms\Delivery $delivery )
    {
        $basketData = $basket->getAll();

        /** @var $obj Data_Object_Order */
        $obj    = $this->createObject();
        $obj->attributes = $form->getData();

        $metro = ! $form->metro ? null : $this->getModel('Metro')->find($form->metro);
        $obj->address   = join(', ', array_filter(array(
            $form->zip,
            $form->country,
            $form->city,
            null === $metro ? false : t('subwey') . $metro->name,
            $form->address,
        )));
        $obj->status    = 1;
        $obj->paid      = 0;
        $obj->date      = time();
        $obj->user_id   = $this->app()->getAuth()->currentUser()->getId();
        $obj->delivery  = 0;
        if ( $delivery->getType() )
            $obj->delivery = $delivery->getType();

        $this->save( $obj );

//        $this->log( $basketData, 'basketData' );


        // Заполняем заказ товарами

        /** @var $opderPositionModel Model_OrderPosition */
        $opderPositionModel = $this->getModel('OrderPosition');

        if ( $obj->getId() ) {
            $pos_list    = array();
            $total_count = 0;
            $total_summa = 0;
            foreach( $basketData as $data ) {
                /** @var $position Data_Object_OrderPosition */
                $position   = $opderPositionModel->createObject();
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

                $total_count += $position->count;
                $total_summa += $position->count * $position->price;
                $pos_list[] = $position->attributes;
            }

            if ( $delivery->getType() ) {
                $total_summa += $delivery->cost();
            }

            $this->app()->getTpl()->assign(array(
                'sitename'  => $this->config->get('sitename'),
                'ord_link'  => $obj->getUrl(),
                'user'      => $this->app()->getAuth()->currentUser()->getAttributes(),
                'date'      => date('H:i d.m.Y'),
                'order_n'   => $obj->getId(),
                'positions' => $pos_list,
                'total_summa'=> $total_summa,
                'total_count'=> $total_count,
                'delivery'  => $delivery->getObject(),
            ));

            sendmail(
                $this->app()->getAuth()->currentUser()->email,
                $this->config->get('admin'),
                'Новый заказ с сайта '.$this->config->get('sitename').' №'.$obj->getId(),
                $this->app()->getTpl()->fetch('system:order.mail.createadmin')
            );

            sendmail(
                $this->config->get('sitename').' <'.$this->config->get('admin').'>',
                $this->app()->getAuth()->currentUser()->email,
                'Новый заказ №'.$obj->getId(),
                $this->app()->getTpl()->fetch('system:order.mail.create')
            );

            return $obj;
        }
        return false;
    }
}
