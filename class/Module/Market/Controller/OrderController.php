<?php
/**
 * Заказы
 * @author keltanas
 * @link http://ermin.ru
 */
namespace Module\Market\Controller;

use Sfcms\Controller;
use Module\Market\Model\OrderModel;
use Module\Market\Object\Order;
use Module\Market\Object\OrderPosition;
use Sfcms\Request;
use Sfcms\Robokassa;

class OrderController extends Controller
{
    public function access()
    {
        return array(
            USER_USER => array('index'),
            USER_ADMIN => array('admin', 'status'),
        );
    }

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
        $this->request->setTitle($this->t('order','My orders'));

        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',$this->t('Home'))
            ->addPiece('user/cabinet',$this->t('user','User cabiner'))
            ->addPiece(null,$this->request->getTitle());


        /** @var $order OrderModel */
        $order  = $this->getModel('Order');

        if ( $this->auth->getPermission() == USER_GUEST ) {
            return $this->redirect("index");
        }

        if ( $cancel ) {
            $can_order = $order->find( $cancel );
            if ($this->auth->getId() == $can_order['user_id']) {
                $can_order['status'] = -1;
            }
        }

        // просмотр заказа
        if ( $item ) {
            /** @var $orderObj Order */
            $orderObj = $order->find( $item );

            if ( $orderObj ) {
                if ( $this->auth->getId() != $orderObj['user_id'] ) {
                    return $this->t('order','Order is not yours');
                }

                $positions = $orderObj->Positions;

                $this->tpl->assign(array(
                    'order' => $orderObj,
                    'list'  => $positions,
                    'all_count' => $positions->sum('count'),
                    'all_summa' => $positions->sum('summa'),
                ));

                return $this->tpl->fetch('order.order');
            }
        }

        $list   = $order->findAll(array(
            'cond'      => sprintf('user_id = ? AND status < ?'),
            'params'    => array($this->auth->getId(), 100),
            'order'     => 'status, date DESC',
        ));

        return array(
            'list' => $list,
        );
    }


    /**
     * Просмотр заказа по ссылке
     * @param int $id
     * @param $code
     * @return mixed
     */
    public function viewAction( $id, $code )
    {
        if ( ! ( $id && $code ) ) {
            throw new \Sfcms_Http_Exception('Order not defined',404);
        }

        $this->request->set('template', 'inner');
        $this->request->setTitle($this->t('order','Checkout'));

//        if ( ! $order_id = $this->app()->getSession()->get('order_id') ) {
//            return t('order','Order not defined');
//        }


        $model = $this->getModel('Order');
        /** @var $order Order */
        $order = $model->find( $id );

        if ( ! $order ) {
            throw new \Sfcms_Http_Exception(sprintf('Order #%d not found', $id), 404);
        }

        if ( ! $order->validateHash( $code ) ) {
            throw new \Sfcms_Http_Exception('Not have permission for viewing this order', 404);
        }

        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',$this->t('Home'))
            ->addPiece('basket',$this->t('basket','Basket'))
            ->addPiece(null, $this->t('order','Checkout'));

        $positions = $order->Positions;
//        $delivery  = $order->Delivery;
        $delivery  = $this->app()->getDelivery($this->request);
        $delivery->setType( $order->delivery_id );
        $payment   = $order->Payment;

        $sum = $positions->sum('sum');

        $robokassa = null;
        switch( $payment->module ) {
            case 'robokassa' :
                $robokassa = new Robokassa( $this->config->get('service.robokassa') );
                $robokassa->setInvId( $order->id );
                $robokassa->setOutSum( $sum + $delivery->cost($sum) );
                $robokassa->setDesc(sprintf('Оплата заказа №%s в интернет-магазине %s',
                                    $order->id, $this->app()->getConfig('sitename')));
                break;
            case 'basket':
            default:
        }

        return array(
            'order'     => $order,
            'positions' => $positions,
            'delivery'  => $delivery,
            'payment'   => $order->Payment,
            'sum'       => $sum,
            'total'     => $delivery->cost($sum) + $sum,
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
        return $this->render('order.admin');
    }

    /**
     * Редактирование заказа
     * @param int $id
     * @param int $new_status
     * @return mixed
     */
    public function adminEdit($id)
    {
        $model = $this->getModel('Order');
        /** @var $order Order */
        $order      = $model->find( $id );
        $positions  = $order->Positions;
        $user       = $order->User;

        if ($this->request->request->has('new_status')) {
            $order->status = $this->request->request->getInt('new_status');
        }

        $this->request->setTitle("Заказ <b>№ {$order->id}</b> от ".strftime('%d.%m.%Y (%H:%M)',$order->date));

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

        return $this->render('order.admin_edit');
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function statusAction($id)
    {
        $model = $this->getModel('Order');
        /** @var $order Order */
        $order      = $model->find( $id );

        if ($this->request->request->has('new_status')) {
            $order->status = $this->request->request->getInt('new_status');
            return $this->renderJson(array('status'=>$order->status, 'msg'=>$this->t('Save successfully')));
        }
        return $this->renderJson(array('error'=>1, 'msg'=>$this->t('Status not defined')));
    }
}
