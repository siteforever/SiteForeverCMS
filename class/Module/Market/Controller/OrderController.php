<?php
namespace Module\Market\Controller;

use Module\Market\Event\PaymentEvent;
use Module\Market\Event\Event;
use Sfcms\Controller;
use Module\Market\Model\OrderModel;
use Module\Market\Object\Order;
use Module\Market\Object\OrderPosition;
use Sfcms\Request;
use Module\Robokassa\Component\Robokassa;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Заказы
 * @author keltanas
 * @link http://ermin.ru
 */
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
     * @return mixed
     * @throws \Sfcms_Http_Exception
     */
    public function indexAction()
    {
        $cancel = $this->request->get('cancel');
        $item   = $this->request->get('item');
        $this->request->set('template', 'inner');
        $this->request->setTitle($this->t('order','My orders'));

        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',$this->t('Home'))
            ->addPiece('user/cabinet',$this->t('user','User cabiner'))
            ->addPiece('order',$this->request->getTitle());


        /** @var $order OrderModel */
        $order  = $this->getModel('Order');

        if ( $this->auth->getPermission() == USER_GUEST ) {
            return $this->redirect("index");
        }

        if ($cancel) {
            $can_order = $order->find($cancel);
            if ($this->auth->getId() == $can_order['user_id']) {
                $can_order['status'] = -1;
            }
        }

        // просмотр заказа
        if ($item) {
            /** @var $orderObj Order */
            $orderObj = $order->find($item);

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

                $this->request->setTitle($this->t('Order #') . $orderObj->id);
                $this->tpl->getBreadcrumbs()->addPiece(null, $this->request->getTitle());

                return $this->tpl->fetch('order.order');
            }
        }

        $list   = $order->findAll(array(
            'cond'      => sprintf('`user_id` = ? AND `status` < ?'),
            'params'    => array($this->auth->getId(), 100),
            'order'     => '`date` DESC',
        ));

        return array(
            'list' => $list,
        );
    }


    /**
     * Просмотр заказа по ссылке
     * @param int $id
     * @param string $code
     *
     * @throws HttpException
     * @return mixed
     */
    public function viewAction($id = null, $code = null)
    {
        if (!($id && $code)) {
            throw new HttpException(404, 'Order not defined');
        }

        $this->request->setTemplate('inner');
        $this->request->setTitle($this->t('order','Checkout'));

        $model = $this->getModel('Order');
        /** @var $order Order */
        $order = $model->find($id);

        if (!$order) {
            throw new HttpException(404, sprintf('Order #%d not found', $id));
        }

        if (!$order->validateHash($code)) {
            throw new HttpException(403, 'Not have permission for viewing this order');
        }

        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',$this->t('Home'))
            ->addPiece('basket',$this->t('basket','Basket'))
            ->addPiece(null, $this->t('order','Checkout'));

        $positions = $order->Positions;
        $delivery  = $this->app->getDeliveryManager($this->request, $order);
        $delivery->setType($order->delivery_id);

        $sum = $positions->sum('sum');

        $paymentEvent = new PaymentEvent($delivery, $order);
        $this->get('event.dispatcher')->dispatch(Event::ORDER_PAYMENT, $paymentEvent);

        return $this->render('order.view', array(
            'order'     => $order,
            'positions' => $positions,
            'delivery'  => $delivery,
            'sum'       => $sum,
            'total'     => $delivery->cost() + $sum,
            'payment'   => $paymentEvent->getPayment(),
        ));
    }

    /**
     * Действия админки
     * @return mixed
     */
    public function adminAction()
    {
        $id     = $this->request->get('id');
        $number = $this->request->get('number');
        $user   = $this->request->get('user');
        $date   = $this->request->get('date');

        if ($id) {
            return $this->adminEditAction($id);
        }

        $model = $this->getModel('Order');

        $cond   = array();
        $params = array();

        if ($number) {
            $cond[]   = " id = ? ";
            $params[] = $number;
        }

        if ($date) {
            if ($tstamp = strtotime($date)) {
                $mon  = date('n', $tstamp);
                $day  = date('d', $tstamp);
                $yer  = date('Y', $tstamp);
                $from = mktime(0, 0, 0, $mon, $day, $yer);
                $to   = mktime(23, 59, 59, $mon, $day, $yer);

                $cond[]   = ' `date` BETWEEN ? AND ? ';
                $params[] = $from;
                $params[] = $to;
            }
        }

        if ($user) {
            $cond[] = " `email` LIKE '%{$user}%'";
        }

        $cond = implode(' AND ', $cond);

        $count = $model->count($cond, $params);

        $paging = $this->paging($count, 10, 'order/admin');

        $orders     = $model->with(array('Positions','Status'))->findAll(array(
            'cond'      => $cond,
            'params'    => $params,
            'order'     => '`date` DESC',
            'limit'     => $paging->limit,
        ));

        $this->request->setTitle('Заказы');
        return $this->render('order.admin', array(
            'orders'    => $orders,
            'paging'    => $paging,
        ));
    }

    /**
     * Редактирование заказа
     * @param int $id
     * @param int $new_status
     * @return mixed
     */
    public function adminEditAction($id)
    {
        /** @var OrderModel $model */
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

        $delivery = $this->app->getDeliveryManager($this->request, $order);
        $delivery->setType($order->delivery_id);
        return $this->render('order.admin_edit', array(
            'order'     => $order,
            'delivery'  => $delivery,
            'positions' => $positions,
            'summa'     => $summa,
            'count'     => $count,
            'statuses'  => $this->getModel('OrderStatus')->findAll(),
            'user'      => $user,
        ));
    }

    /**
     * @param int $id
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
