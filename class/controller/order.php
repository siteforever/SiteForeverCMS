<?php
/**
 * Заказы
 * @author keltanas
 * @link http://ermin.ru
 */
class Controller_Order extends Sfcms_Controller
{
    /**
     * @return mixed
     */
    public function indexAction()
    {
        $this->request->set('template', 'inner');
        $this->request->setTitle('Мои заказы');

        $user   = $this->app()->getAuth()->currentUser();

        /** @var $order Model_Order */
        $order  = $this->getModel('Order');

        if ( ! $user->getId() || $user->perm == USER_GUEST ) {
            return $this->redirect("users/login");
        }

        if ( $cancel = $this->request->get('cancel') ) {
            $can_order = $order->find( $cancel );
            if( $can_order['user_id'] == $user->getId() ) {
                $can_order['status'] = '100';
//                $order->update( $can_order );
            }
        }

        // просмотр заказа
        $item = $this->request->get('item', Request::INT);

        if ( $item ) {
            /** @var $orderObj Data_Object_Order */
            $orderObj = $order->find( $item );
            
            if ( $orderObj ) {
                if ( $this->user->get('id') != $orderObj['user_id'] ) {
                    return t('order','Order is not yours');
                }

                /** @var $list_pos Data_Collection */
                $list_pos = $orderObj->positions;
                $this->log( $list_pos, 'positions' );
                $all_count = 0;
                $all_summa = 0;

                /** @var $position Data_Object_OrderPosition */
                foreach( $list_pos as $position ) {
                    $all_count += $position['count'];
                    $all_summa += $position['summa'];
                }
                $this->tpl->assign(array(
                    'order' => $orderObj,
                    'list'  => $list_pos,
                    'all_count' => $all_count,
                    'all_summa' => $all_summa,
                ));

                return $this->tpl->fetch('system:order.order');
            }
        }

        $list   = $order->findAll(array(
            'cond'      => 'user_id = ? AND status < 100',
            'params'    => array( $user->getId() ),
            'order'     => 'status, date DESC',
        ));

        $this->tpl->assign('list', $list);
        return $this->tpl->fetch('system:order.index');
    }

    /**
     * Создать заказ
     * @return mixed
     */
    public function createAction()
    {
        $this->request->set('template', 'inner');
        $this->request->setTitle(t('order','Checkout'));

        // todo Сделать возможность заказа для гостей
        // проверить, зарегистрирован ли клиент
        if ( ! $this->user->hasPermission( USER_USER ) ) {
            return $this->tpl->fetch('system:order.need');
        }

        if ( $this->getBasket()->getCount() == 0 ) {
            return t('order', 'Your basket is empty');
        }

        //$cat = $this->getModel('Catalog');

        // готовим список к выводу
        $all_product = $this->getBasket()->getAll();

        $all_keys = array();
        foreach( $all_product as $prod ) {
            $all_keys[] = $prod['id'];
        }
        /*$goods = $cat->findGoodsById( $all_keys );

        foreach( $all_product as $key => &$product )
        {
            $product['cat_id']  = $product['id'];
            $product['name']    = $goods[$product['id']]['name'];
            $product['price']   =
                    $this->user->getPermission() == USER_WHOLE ?
                            $goods[$product['id']]['price2'] :
                            $goods[$product['id']]['price1'];
            $product['summa']   = $product['price'] * $product['count'];
            $product['articul'] = $goods[$product['id']]['articul'];
        }*/

        //printVar( $all_product );

        // Создание заказа
        $complete = $this->request->get('complete');
        if ( $complete && $all_product ) {
            /** @var $orderModel Model_Order */
            $orderModel    = $this->getModel('Order');
            $orderModel->createOrder( $all_product );
            $this->getBasket()->clear();
            return $this->redirect('order');
        }


        $this->tpl->assign(array(
            'products' => $all_product,
        ));
        $this->request->setContent($this->tpl->fetch('system:order.create'));
    }

    /**
     * Действия админки
     * @return mixed
     */
    public function adminAction()
    {
        if ( $num = $this->request->get('id', FILTER_VALIDATE_INT) ) {
            return $this->adminEdit( $num );
        }

        $model = $this->getModel('Order');

        $cond   = array();
        $params = array();

        if ( $number = $this->request->get('number', FILTER_VALIDATE_INT) ) {
            $cond[]             = " id = :number ";
            $params[':number']  = $number;
        }

        if ( $date = $this->request->get('date') ) {
            if ( $tstamp = strtotime( $date ) ) {
                $mon    = date('n', $tstamp);
                $day    = date('d', $tstamp);
                $yer    = date('Y', $tstamp);
                $from   = mktime(0,0,0,$mon,$day,$yer);
                $to     = mktime(23,59,59,$mon,$day,$yer);

                $cond[]     = ' `date` BETWEEN :from AND :to ';
                $params[':from']    = $from;
                $params[':to']      = $to;
            }
        }

        if ( $this->request->get('user') ) {
            $user_model = $this->getModel('User');
            $users  = $user_model->findAll(array(
                'select'    => 'id',
                'cond'      => ' ( login LIKE :user OR email LIKE :user '.
                        ' OR lname LIKE :user OR name LIKE :user ) ',
                'params'    => array(':user'=>$this->request->get('user')),
            ));
            $users_id    = array();
            if ( $users ) {
                foreach ( $users as $u ) {
                    $users_id[] = $u->getId();
                }
            }
            if ( $users_id && count( $users_id ) ) {
                $cond[]     = ' user_id IN ( :users )';
                $params[':users']   = $users_id;
            }
        }

        $cond   = implode(' AND ', $cond);

        $count  = $model->count( $cond, $params );

        //$count = $model->getCountForAdmin($cond);

        $paging = $this->paging( $count, 20, 'admin/order' );

        $orders     = $model->findAll(array(
            'cond'      => $cond,
            'params'    => $params,
            'order'     => '`date` DESC',
            'limit'     => $paging->limit,
        ));

        //$orders = $model->findAllForAdmin($cond, $paging['offset'].','.$paging['perpage']);

        $this->tpl->assign(array(
            'orders'    => $orders,
            'paging'    => $paging,
        ));

        $this->request->setTitle('Заказы');
        $this->request->setContent($this->tpl->fetch('system:order.admin'));
    }

    /**
     * Редактирование заказа
     * @param int $num
     * @return mixed
     */
    public function adminEdit( $num )
    {
        $this->request->setTitle('Править заказ');

        $model = $this->getModel('Order');



        $order      = $model->find( $num );
        $positions  = $order->Positions;
        $user       = $this->getModel('User')->find( $order['user_id'] );

        if ( $new_status = $this->request->get('new_status', FILTER_VALIDATE_INT) )
        {
            $order['status'] = $new_status;
            $model->update( $order );
        }

        $summa = 0;
        $count = 0;
        foreach( $positions as $key => $pos )
        {
            $summa += $pos->sum;
            $count += $pos->count;
        }

        $this->tpl->assign(array(
            'order'     => $order,
            'positions' => $positions,
            'summa'     => $summa,
            'count'     => $count,
            'statuses'  => $this->getModel('OrderStatus')->findAll(),
            'user'      => $user,
        ));

        $this->request->setContent($this->tpl->fetch('system:order.admin_edit'));
    }
}
