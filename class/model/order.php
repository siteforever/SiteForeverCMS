<?php
/**
 * Модель заказов
 * @author Nikolay Ermin 
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
class model_Order extends Model
{
    protected $positions = array();

    protected $statuses;

    function createTables()
    {
        if ( ! $this->isExistTable( DBORDER ) ) {
            $this->db->query("CREATE TABLE `".DBORDER."` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `status` tinyint(4) NOT NULL default '0',
              `date` int(11) NOT NULL default '0',
              `user_id` int(11) NOT NULL default '0',
              PRIMARY KEY  (`id`),
              KEY `status` (`status`),
              KEY `user_id` (`date`,`user_id`),
              KEY `date` (`date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
        if ( ! $this->isExistTable( DBORDERPOS ) ) {
            $this->db->query("CREATE TABLE `".DBORDERPOS."` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `ord_id` int(11) NOT NULL default '0',
              `name` varchar(250) NOT NULL default '',
              `articul` varchar(250) NOT NULL default '',
              `details` text NOT NULL,
              `currency` varchar(10) NOT NULL default 'руб.',
              `item` varchar(10) NOT NULL default 'шт',
              `cat_id` int(11) NOT NULL default '0',
              `price` decimal(13,2) NOT NULL default '0.00',
              `count` int(11) NOT NULL default '0',
              `status` decimal(13,2) NOT NULL default '0.00',
              PRIMARY KEY  (`id`),
              KEY `ord_id` (`ord_id`),
              KEY `cat_id` (`cat_id`),
              KEY `name` (`name`),
              KEY `articul` (`articul`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
        if ( ! $this->isExistTable( DBORDERSTATUS ) ) {
            $this->db->query("CREATE TABLE `".DBORDERSTATUS."` (
              `id` int(11) NOT NULL auto_increment,
              `status` int(11) NOT NULL default '0',
              `name` varchar(100) NOT NULL default '',
              PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
    }

    function find( $id )
    {
        if ( !isset( $this->data[$id] ) || !$this->data[$id] ) {
            $data = $this->db->fetch(
                "SELECT * FROM `".DBORDER."` WHERE id = {$id} LIMIT 1"
            );
            $this->data[$id] = $data;
        }
        return $this->data[$id];
    }

    /**
     * Вернет список статусов
     * @return array
     */
    function getStatuses()
    {
        if ( is_null( $this->statuses ) ) {
            $data = $this->db->fetchAll("SELECT * FROM ".DBORDERSTATUS." ORDER BY status");
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
     * Количество заказов
     * @return string
     */
    function getCountForAdmin( $cond = array() )
    {
        $where = '';
        if ( count( $cond ) ) {
            $where = 'WHERE '.join(' AND ', $cond);
        }
        return $this->db->fetchOne(
            "SELECT COUNT(*) FROM `".DBORDER."` ord
            INNER JOIN ".DBUSERS." u ON  u.id = ord.user_id 
            {$where}"
        );
    }

    /**
     * Список заказов для админки
     * @param string $limit
     * @return array
     */
    function findAllForAdmin( $cond = array(), $limit = '' )
    {
        $where = '';
        if ( count( $cond ) ) {
            $where = 'WHERE '.join(' AND ', $cond);
        }
        $list = $this->db->fetchAll(
            "SELECT ord.*, COUNT(op.id) pos_num, SUM(op.count) count, SUM(op.count * op.price) summa, u.email
            FROM `".DBORDER."` ord
            INNER JOIN ".DBUSERS." u ON u.id = ord.user_id
            LEFT JOIN ".DBORDERPOS." op ON op.ord_id = ord.id
            {$where}
            GROUP BY ord.id
            ORDER BY ord.date DESC".($limit?' LIMIT '.$limit : '')
        );
        foreach ( $list as &$l ) {
            $l['status_value'] = $this->getStatus( $l['status'] );
        }
        return $list;
    }

    /**
     * Сохранить данные
     * @param array $data
     * @return void
     */
    function update( $data )
    {
        return $this->db->update( DBORDER, $data, " id = {$data['id']} " );
    }

    /**
     * Создать заказ
     * @param array $basket_data
     * @return void
     */
    function createOrder( $basket_data )
    {
        $ord_id = $this->db->insert( DBORDER, array(
            //'id'        => '',
            'status'    => '0',
            'date'      => time(), 
            'user_id'   => App::$user->get('id'),
        ));
        if ( $ord_id ) {
            $ret = true;
            $pos_list = array();
            $total_count = 0;
            $total_summa = 0;
            foreach( $basket_data as $data ) {
                $pos_data = array(
                    //'id'        => '0',
                    'ord_id'    => $ord_id,
                    'name'      => $data['name'],
                    'articul'   => $data['articul'],
                    'details'   => $data['details'],
                    'currency'  => $data['currency'],
                    'item'      => $data['item'],
                    'cat_id'    => $data['cat_id'],
                    'price'     => $data['price'],
                    'count'     => $data['count'],
                    'status'    => 0,
                );
                $ret &= $this->db->insert( DBORDERPOS, $pos_data );
                $pos_data['summa'] = $pos_data['count'] * $pos_data['price'];
                $total_count += $pos_data['count'];
                $total_summa += $pos_data['summa'];
                $pos_list[] = $pos_data;
            }

            App::$tpl->assign(array(
                'sitename'  => App::$config->get('sitename'),
                'ord_link'  => App::$config->get('siteurl').App::$router->createLink('order',array('item'=>$ord_id)),
                'user'      => App::$user->getData(),
                'date'      => date('H:i d.m.Y'),
                'order_n'   => $ord_id,
                'positions' => $pos_list,
                'total_summa'=> $total_summa,
                'total_count'=> $total_count,
            ));

            $msg = App::$tpl->fetch('system:order.mail_create');

            //print $msg;

            sendmail(
                 App::$config->get('sitename').' <'.App::$config->get('admin').'>',
                App::$user->get('email'),
                'Новый заказ №'.$ord_id,
                $msg
            );

            return $ret;
        }
        return false;
    }
}
