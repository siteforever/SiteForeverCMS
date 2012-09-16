<?php
/**
 * Заказы
 * @author keltanas
 * @link http://ermin.ru
 */
class Controller_Order extends Sfcms_Controller
{
    /**
     * Список заказов зарегистрированного пользователя
     * @param int $cancel
     * @param int $item
     * @return mixed
     * @throws \Sfcms_Http_Exception
     */
    public function indexAction( $cancel, $item )
    {
        $this->request->set('template', 'inner');
        $this->request->setTitle(t('order','My orders'));

        $this->tpl->getBreadcrumbs()
            ->addPiece('index',t('Home'))
            ->addPiece('users/cabinet',t('user','User cabiner'))
            ->addPiece(null,$this->request->getTitle());


        $user   = $this->app()->getAuth()->currentUser();

        if ( ! $user->hasPermission( USER_USER ) ) {
            throw new \Sfcms_Http_Exception( t('Access denied'), 403 );
        }

        /** @var $order Model_Order */
        $order  = $this->getModel('Order');

        if ( ! $user->getId() || $user->perm == USER_GUEST ) {
            return $this->redirect("index");
        }

        if ( $cancel ) {
            $can_order = $order->find( $cancel );
            if( $can_order['user_id'] == $user->getId() ) {
                $can_order['status'] = -1;
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

                $positions = $orderObj->Positions;
                $this->log( $positions, 'positions' );
                $all_count = 0;
                $all_summa = 0;

                /** @var $position Data_Object_OrderPosition */
                foreach( $positions as $position ) {
                    $all_count += $position['count'];
                    $all_summa += $position['summa'];
                }
                $this->tpl->assign(array(
                    'order' => $orderObj,
                    'list'  => $positions,
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
        $order_id = @$_SESSION['order_id'];
        if ( ! $order_id ) {
            return 'Order not defined';
        }
        $this->request->set('template', 'inner');
        $this->request->setTitle(t('order','Checkout'));

        $model = $this->getModel();
        /** @var $order Data_Object_Order */
        $order = $model->find( $order_id );

        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',t('Home'))
            ->addPiece('basket',t('basket','Basket'))
            ->addPiece(null, t('order','Checkout'));

        $positions = $order->Positions;
        $delivery  = $order->Delivery;
        $payment   = $order->Payment;

        $sum = $positions->sum('sum');

        $robokassa = null;
        switch( $payment->module ) {
            case 'robokassa' :
                $robokassa = new \Sfcms\Robokassa( $this->config->get('service.robokassa') );
                $robokassa->setInvId( $order->id );
                $robokassa->setOutSum( $sum + ($delivery?$delivery->cost:0) );
                break;
            case 'basket':
            default:
        }


        return array(
            'order' => $order,
            'products' => $positions,
            'delivery' => $delivery,
            'payment'   => $order->Payment,
            'sum'   => $delivery->cost + $positions->sum('sum'),
            'robokassa' => $robokassa,
        );
    }

    /**
     * Действия админки
     * @param int $id
     * @param int $number
     * @param string $user
     * @return mixed
     */
    public function adminAction( $id, $number, $user )
    {
        if ( $id ) {
            return $this->adminEdit( $id );
        }

        $model = $this->getModel('Order');

        $cond   = array();
        $params = array();

        if ( $number ) {
            $cond[]   = " id = ? ";
            $params[] = $number;
        }

        if ( $date = $this->request->get('date') ) {
            if ( $tstamp = strtotime( $date ) ) {
                $mon    = date('n', $tstamp);
                $day    = date('d', $tstamp);
                $yer    = date('Y', $tstamp);
                $from   = mktime(0,0,0,$mon,$day,$yer);
                $to     = mktime(23,59,59,$mon,$day,$yer);

                $cond[]   = ' `date` BETWEEN ? AND ? ';
                $params[] = $from;
                $params[] = $to;
            }
        }

        if ( $user ) {
            $cond[]     = " `email` LIKE '%{$user}%'";
        }

        $cond   = implode(' AND ', $cond);

        $count  = $model->count( $cond, $params );

        //$count = $model->getCountForAdmin($cond);

        $paging = $this->paging( $count, 10, 'order/admin' );

        $orders     = $model->with(array('Positions','Status'))->findAll(array(
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
     * @param int $id
     * @return mixed
     */
    public function adminEdit( $id )
    {
        $model = $this->getModel('Order');
        /** @var $order Data_Object_Order */
        $order      = $model->find( $id );
        $positions  = $order->Positions;
        $user       = $order->User;

        if ( $new_status = $this->request->get('new_status', FILTER_VALIDATE_INT) ) {
            $order->status = $new_status;
        }

        $this->request->setTitle("Заказ <b>№ {$order->id}</b> от ".strftime('%d.%m.%Y (%H:%M)'));

        $summa = $positions->sum('sum');
        $count = $positions->sum('count');

        $this->tpl->assign(array(
            'order'     => $order,
            'positions' => $positions,
            'summa'     => $summa,
            'count'     => $count,
            'statuses'  => $this->getModel('OrderStatus')->findAll(),
            'user'      => $user,
        ));

        return $this->tpl->fetch('system:order.admin_edit');
    }
}
