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
     * @param array $basketData
     * @param Data_Object_Delivery $delivery
     * @return bool|Data_Object_Order
     */
    public function createOrder( $basketData, Forms_Basket_Address $form, Data_Object_Delivery $delivery )
    {
        /** @var $obj Data_Object_Order */
        $obj    = $this->createObject();
        $obj->attributes = $form->getData();
        $obj->status    = 1;
        $obj->paid      = 0;
        $obj->date      = time();
        $obj->user_id   = $this->app()->getAuth()->currentUser()->getId();
        $obj->delivery  = 0;
        if ( $delivery )
            $obj->delivery = $delivery->id;

        $this->save( $obj );

//        $this->log( $basketData, 'basketData' );

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
                    'articul'   => ! empty( $data['articul'] ) ? $data['articul'] : $data['name'],
                    'details'   => $data['details'],
                    'currency'  => $data['currency'],
                    'item'      => $data['item'],
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

            if ( $delivery ) {
                $total_summa += $delivery->cost;
            }

            $this->app()->getTpl()->assign(array(
                'sitename'  => $this->config->get('sitename'),
                'ord_link'  => $this->config->get('siteurl').$this->app()->getRouter()->createLink('order',array('item'=>$obj->getId())),
                'user'      => $this->app()->getAuth()->currentUser()->getAttributes(),
                'date'      => date('H:i d.m.Y'),
                'order_n'   => $obj->getId(),
                'positions' => $pos_list,
                'total_summa'=> $total_summa,
                'total_count'=> $total_count,
                'delivery'  => $delivery,
            ));

            $msg = $this->app()->getTpl()->fetch('system:order.mail_create');

            //print $msg;

            sendmail(
                $this->app()->getAuth()->currentUser()->email,
                $this->config->get('admin'),
                'Новый заказ с сайта '.$this->config->get('sitename').' №'.$obj->getId(),
                $msg
            );

            sendmail(
                $this->config->get('sitename').' <'.$this->config->get('admin').'>',
                $this->app()->getAuth()->currentUser()->email,
                'Новый заказ №'.$obj->getId(),
                $msg
            );

            return $obj;
        }
        return false;
    }
}
