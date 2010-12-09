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
        App::$request->set('template', 'inner');
        App::$request->set('tpldata.page.title', 'Мои заказы');
        App::$request->set('tpldata.page.content', '');

        $order = Model::getModel('model_Order');

        if ( !App::$user->get('id') || App::$user->getPermission() == USER_GUEST ) {
            redirect("users/login");
        }

        if ( $cancel = App::$request->get('cancel') )
        {
            //print "cancel:$cancel";
            $can_order = $order->find( $cancel );
            if( $can_order['user_id'] == App::$user->get('id') )
            {
                $can_order['status'] = '100';
                $order->update( $can_order );
            }
            //printVar($can_order);
        }

        // просмотр заказа
        $item = App::$request->get('item', Request::INT);
        if ( $item )
        {
            $order_data = $order->find( $item );
            if ( $order_data )
            {
                if ( App::$user->get('id') != $order_data['user_id'] ) {
                    App::$request->addFeedback('Заказ вам не принадлежит');
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
                App::$tpl->assign(array(
                    'order' => $order_data,
                    'list'  => $list_pos,
                    'all_count' => $all_count,
                    'all_summa' => $all_summa,
                ));

                App::$request->set('tpldata.page.content', App::$tpl->fetch('system:order.order'));
                return;
            }
        }

        $list = $order->findAllByUserId( App::$user->get('id') );
        App::$tpl->assign('list', $list);
        App::$request->set('tpldata.page.content', App::$tpl->fetch('system:order.index'));
    }

    /**
     * Создать заказ
     * @return
     */
    function createAction()
    {
        App::$request->set('template', 'inner');
        App::$request->set('tpldata.page.title', 'Оформить заказ');
        App::$request->set('tpldata.page.content', 'Оформить заказ');

        // проверить, зарегистрирован ли клиент
        if ( App::$user->getPermission() == USER_GUEST )
        {
            App::$request->set(
                'tpldata.page.content',
                App::$tpl->fetch('system:order.need')
            );
            return;
        }

        if ( App::$basket->getCount() == 0 ) {
            App::$request->set(
                'tpldata.page.content',
                    'Ваша <a '.href('basket').'>корзина</a> пуста'
            );
            return;
        }

        $cat = Model::getModel('model_Catalog');

        // готовим список к выводу
        $all_product = App::$basket->getAll();

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
                    App::$user->getPermission() == USER_WHOLE ?
                            $goods[$product['id']]['price2'] :
                            $goods[$product['id']]['price1'];
            $product['summa']   = $product['price'] * $product['count'];
            $product['articul'] = $goods[$product['id']]['articul'];
        }

        //printVar( $all_product );

        // Создание заказа
        $complete = App::$request->get('complete');
        if ( $complete && $all_product ) {
            //App::$request->debug();
            $ord = Model::getModel('model_Order');
            $ord->createOrder( $all_product );
            App::$basket->clear();
            redirect('order');
        }


        App::$tpl->assign(array(
            'products' => $all_product,
        ));
        App::$request->set('tpldata.page.content', App::$tpl->fetch('system:order.create'));
    }

    /**
     * Действия админки
     * @return void
     */
    function adminAction()
    {
        if ( $num = App::$request->get('num', FILTER_VALIDATE_INT) ) {
            return $this->adminEdit( $num );
        }

        $model = model::getModel('model_Order');

        $conds = array();
        if ( $number = App::$request->get('number', FILTER_VALIDATE_INT) ) {
            $cond[] = " ord.id = '{$number}' ";
        }
        if ( $date = App::$request->get('date') ) {
            if ( $tstamp = strtotime( $date ) ) {
                $mon    = date('n', $tstamp);
                $day    = date('d', $tstamp);
                $yer    = date('Y', $tstamp);
                $from   = mktime(0,0,0,$mon,$day,$yer);
                $to     = mktime(23,59,59,$mon,$day,$yer);
                $cond[] = " ord.date BETWEEN $from AND $to ";
            }
        }
        if ( $user = App::$request->get('user') ) {
            $cond[] =  " ( u.login LIKE '%{$user}%' OR u.email LIKE '%{$user}%' ".
                        " OR u.lname LIKE '%{$user}%' OR u.name LIKE '%{$user}%' ) ";
        }

        $count = $model->getCountForAdmin($cond);

        $paging = $this->paging( $count, 20, 'admin/order' );
        $orders = $model->findAllForAdmin($cond, $paging['offset'].','.$paging['perpage']);

        App::$tpl->assign(array(
            'orders'    => $orders,
            'paging'    => $paging,
        ));

        App::$request->set('tpldata.page.title', 'Заказы');
        App::$request->set('tpldata.page.content', App::$tpl->fetch('system:order.admin'));
    }

    /**
     * Редактирование заказа
     * @param int $num
     * @return void
     */
    function adminEdit( $num )
    {
        App::$request->set('tpldata.page.title', 'Править заказ');

        $model = model::getModel('model_Order');



        $order      = $model->find( $num );
        $positions  = $model->findPositionsByOrderId( $num );
        $user       = App::$user->find( $order['user_id'] );

        if ( $new_status = App::$request->get('new_status', FILTER_VALIDATE_INT) )
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

        App::$tpl->assign(array(
            'order'     => $order,
            'positions' => $positions,
            'summa'     => $summa,
            'count'     => $count,
            'statuses'  => $model->getStatuses(),
            'user'      => $user,
        ));

        App::$request->set('tpldata.page.content', App::$tpl->fetch('system:order.admin_edit'));
    }
}
