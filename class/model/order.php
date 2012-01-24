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

    /**
     * @var Model_OrderStatus
     */
    public  $model_status;

    /**
     * @var Model_OrderPosition
     */
    public  $model_position;

    /**
     * Инициализация
     * @return void
     */
    protected function init()
    {
        $this->model_position   = $this->getModel('OrderPosition');
        $this->model_status     = $this->getModel('OrderStatus');
    }

    /**
     * Отношения
     * @return array
     */
    function relation()
    {
        return array(
            'positions'     => array( self::HAS_MANY, 'OrderPosition', 'ord_id' ),
            'count'         => array( self::STAT,     'OrderPosition', 'ord_id' ),
            'statusObj'     => array( self::HAS_ONE,  'OrderStatus', 'status' ),
        );
    }

    /**
     * Вернет список статусов
     * @return array
     */
    function getStatuses()
    {
        $order_status   = $this->getModel('OrderStatus');
        
        if ( is_null( $this->statuses ) ) {
            $data   = $order_status->findAll(array(
                'order' => 'status'
            ));
            $this->statuses = array();
            foreach( $data as $d ) {
                $this->statuses[$d['status']] = $d['name'];
            }
        }
        return $this->statuses;
    }

    /**
     * Вернет статус для значения или false
     * @param int $status
     * @return bool/string
     */
    function getStatus( $status )
    {
        $statuses = $this->getStatuses();
        if ( isset($statuses[$status]) ) {
            return $statuses[$status];
        }
        return false;
    }

    /**
     * Поиск по Id пользователя
     * @deprecated
     * @param  $id
     * @return array
     */
    function findAllByUserId( $id )
    {
        $list = $this->db->fetchAll(
            "SELECT o.*, SUM( op.count ) count, SUM( op.price * op.count ) summa
            FROM `".DBORDER."` o
                LEFT JOIN ".DBORDERPOS." op ON o.id = op.ord_id
            WHERE o.user_id = {$id} AND o.status < 100
            GROUP BY o.id
            ORDER BY o.status, o.date DESC"
        );
        if ( !$list ) {
            $list = array();
        }
        foreach ( $list as &$l ) {
            $l['status_value'] = $this->getStatus( $l['status'] );
        }
        return $list;
    }


    /**
     * Список позиций к заказу
     * @deprecated
     * @param  $id
     * @return void
     */
    function findPositionsByOrderId( $id )
    {
        $list = $this->db->fetchAll(
            "SELECT * FROM ".DBORDERPOS." WHERE ord_id = {$id}"
        );
        return $list;
    }

    /**
     * Создать заказ
     * @param array $basket_data
     * @return void
     */
    function createOrder( $basket_data )
    {
        $obj    = $this->createObject(array(
            'status'    => 0,
            'date'      => time(),
            'user_id'   => $this->app()->getAuth()->currentUser()->getId(),
        ));

        $this->save( $obj );

        if ( $obj->getId() ) {
            $ret = true;
            $pos_list = array();
            $total_count = 0;
            $total_summa = 0;
            foreach( $basket_data as $data ) {
                $position   = $this->model_position->createObject(array(
                    'ord_id'    => $obj->getId(),
                    'name'      => $data['name'],
                    'articul'   => $data['articul'],
                    'details'   => $data['details'],
                    'currency'  => $data['currency'],
                    'item'      => $data['item'],
                    'cat_id'    => is_numeric( $data['id'] ) ? $data['id'] : '0',
                    'price'     => $data['price'],
                    'count'     => $data['count'],
                    'status'    => 0,
                ));

                $this->model_position->save( $position );

                $ret = $position->getId() ? $ret : false;

                $total_count += $position->count;
                $total_summa += $position->count * $position->price;
                $pos_list[] = $position->getAttributes();
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
            ));

            $msg = $this->app()->getTpl()->fetch('system:order.mail_create');

            //print $msg;

            sendmail(
                $this->config->get('sitename').' <'.$this->config->get('admin').'>',
                $this->app()->getAuth()->currentUser()->email,
                'Новый заказ №'.$obj->getId(),
                $msg
            );

            return $ret;
        }
        return false;
    }
}
