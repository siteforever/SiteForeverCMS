<?php
/**
 * Заказы
 * @author keltanas
 * @link http://ermin.ru
 */
class controller_Order extends Controller
{
    function indexAction()
    {
        $this->request->set('template', 'inner');
        $this->request->setTitle('Мои заказы');
        $this->request->setContent('');
        
        $order  = $this->getModel('Order');

        if ( !$this->user->get('id') || $this->user->getPermission() == USER_GUEST ) {
            redirect("users/login");
        }

        if ( $cancel = $this->request->get('cancel') )
        {
            //print "cancel:$cancel";
            $can_order = $order->find( $cancel );
            if( $can_order['user_id'] == $this->user->get('id') )
            {
                $can_order['status'] = '100';
                $order->update( $can_order );
            }
            //printVar($can_order);
        }

        // просмотр заказа
        $item = $this->request->get('item', Request::INT);
        if ( $item )
        {
            $order_data = $order->find( $item );
            if ( $order_data )
            {
                if ( $this->user->get('id') != $order_data['user_id'] ) {
                    $this->request->addFeedback('Заказ вам не принадлежит');
                    return;
                }

                $list_pos = $order->findPositionsByOrderId( $item );
                $all_count = 0;
                $all_summa = 0;
                
                foreach( $list_pos as &$position )
                {
                    $position['summa'] = $position['count'] * $position['price'];
                    $all_count += $position['count'];
                    $all_summa += $position['summa'];
                }
                $this->tpl->assign(array(
                    'order' => $order_data,
                    'list'  => $list_pos,
                    'all_count' => $all_count,
                    'all_summa' => $all_summa,
                ));

                $this->request->setContent( $this->tpl->fetch('system:order.order'));
                return;
            }
        }

        $list = $order->findAllByUserId( $this->user->get('id') );
        $this->tpl->assign('list', $list);
        $this->request->setContent( $this->tpl->fetch('system:order.index') );
    }

    /**
     * Создать заказ
     * @return
     */
    function createAction()
    {
        $this->request->set('template', 'inner');
        $this->request->setTitle('Оформить заказ');
        $this->request->setContent('Оформить заказ');

        // проверить, зарегистрирован ли клиент
        if ( $this->user->getPermission() == USER_GUEST )
        {
            $this->request->setContent( $this->tpl->fetch('system:order.need') );
            return;
        }

        if ( $this->basket->getCount() == 0 ) {
            $this->request->setContent( 'Ваша <a '.href('basket').'>корзина</a> пуста' );
            return;
        }

        $cat = $this->getModel('Catalog');

        // готовим список к выводу
        $all_product = $this->basket->getAll();

        $all_keys = array();
        foreach( $all_product as $prod ) {
            $all_keys[] = $prod['id'];
        }
        $goods = $cat->findGoodsById( $all_keys );

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
        }

        //printVar( $all_product );

        // Создание заказа
        $complete = $this->request->get('complete');
        if ( $complete && $all_product ) {
            //$this->request->debug();
            $ord = $this->getModel('Order');
            $ord->createOrder( $all_product );
            $this->basket->clear();
            redirect('order');
        }


        $this->tpl->assign(array(
            'products' => $all_product,
        ));
        $this->request->setContent($this->tpl->fetch('system:order.create'));
    }

    /**
     * Действия админки
     * @return void
     */
    function adminAction()
    {
        if ( $num = $this->request->get('num', FILTER_VALIDATE_INT) ) {
            return $this->adminEdit( $num );
        }

        $model = $this->getModel('Order');

        $cond = array();
        if ( $number = $this->request->get('number', FILTER_VALIDATE_INT) ) {
            $cond[] = " ord.id = '{$number}' ";
        }
        if ( $date = $this->request->get('date') ) {
            if ( $tstamp = strtotime( $date ) ) {
                $mon    = date('n', $tstamp);
                $day    = date('d', $tstamp);
                $yer    = date('Y', $tstamp);
                $from   = mktime(0,0,0,$mon,$day,$yer);
                $to     = mktime(23,59,59,$mon,$day,$yer);
                $cond[] = " ord.date BETWEEN $from AND $to ";
            }
        }
        if ( $user = $this->request->get('user') ) {
            $cond[] =  " ( u.login LIKE '%{$user}%' OR u.email LIKE '%{$user}%' ".
                        " OR u.lname LIKE '%{$user}%' OR u.name LIKE '%{$user}%' ) ";
        }

        $count = $model->getCountForAdmin($cond);

        $paging = $this->paging( $count, 20, 'admin/order' );
        $orders = $model->findAllForAdmin($cond, $paging['offset'].','.$paging['perpage']);

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
     * @return void
     */
    function adminEdit( $num )
    {
        $this->request->setTitle('Править заказ');

        $model = $this->getModel('Order');



        $order      = $model->find( $num );
        $positions  = $model->findPositionsByOrderId( $num );
        $user       = $this->user->find( $order['user_id'] );

        if ( $new_status = $this->request->get('new_status', FILTER_VALIDATE_INT) )
        {
            $order['status'] = $new_status;
            $model->update( $order );
        }

        $summa = 0;
        $count = 0;
        foreach( $positions as $key => $pos )
        {
            $positions[$key]['summa'] = $pos['price'] * $pos['count'];
            $summa += $positions[$key]['summa'];
            $count += $pos['count'];
        }

        $this->tpl->assign(array(
            'order'     => $order,
            'positions' => $positions,
            'summa'     => $summa,
            'count'     => $count,
            'statuses'  => $model->getStatuses(),
            'user'      => $user,
        ));

        $this->request->setContent($this->tpl->fetch('system:order.admin_edit'));
    }
}
