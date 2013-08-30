<?php
/**
 * Контроллер корзины
 * @author KelTanas
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
namespace Module\Market\Controller;

use Module\Market\Event\OrderEvent;
use Module\Market\Model\OrderPositionModel;
use Module\Market\Object\OrderPosition;
use Sfcms;
use Sfcms\Controller;
use Sfcms\Form\Form;
use Forms_Basket_Address;
use Module\Market\Object\Delivery;
use Module\Market\Model\OrderModel;
use Module\Catalog\Model\CatalogModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BasketController extends Controller
{
    public function indexAction()
    {
        $this->logger->info('request', $this->request->request->all());

        $address = $this->request->get('address');
        $form = new Forms_Basket_Address();
        $form->delivery_id = $this->request->getSession()->get('delivery');

        // Ajax validate
        if ($form->getPost($this->request)) {
            $result = $this->formValidate($form);
            $this->logger->info('Form validate result', $result);
            if ($this->request->isXmlHttpRequest()) {
                return $result;
            }
            if (isset($result['redirect'])) {
                return $this->redirect($result['redirect']);
            }
        }

        // Fill Address from current user
        if ( $this->auth->hasPermission( USER_USER ) ) {
            $form->setData($this->auth->currentUser()->attributes);
        }

        // Fill Address from Yandex
        if ($address) {
            $form->setData($this->fromYandexAddress($address));
        }

        $this->request->setTitle($this->t('basket','Basket'));
        $this->request->set('template', 'inner');

        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',$this->t('Home'))
            ->addPiece(null,$this->request->getTitle());

        // Заполним методы доставки
        $deliveryModel = $this->getModel('Delivery');
        $deliveries    = $deliveryModel->findAll('active = ?', array(1), 'pos');
        $form->getField('delivery_id')->setVariants($deliveries->column('name'));

        $delivery = $this->app->getDelivery($this->request);
        $form->getField('delivery_id')->setValue($delivery->getType());

        // Заполним методы оплаты
        $paymentModel = $this->getModel('Payment');
        $payments = $paymentModel->findAll('active = ?', array(1));
        $form->getField('payment_id')->setVariants($payments->column('name'));
        $form->getField('payment_id')->setValue($payments->rewind() ? $payments->rewind()->getId() : 0);

        if ($form->getField('metro')) {
            $metroModel = $this->getModel('Metro');
            $metro      = $metroModel->findAll('city_id = ?', array(2), 'name');
            $form->getField('metro')->setVariants($metro->column('name'));
        }

        // Список ID продуктов
        $productIds = array_filter(array_map(function ($b) {
            return isset($b['id']) ? $b['id'] : false;
        }, $this->getBasket()->getAll()));

        // Получаем товары из каталога
        /** @var $catalogModel CatalogModel */
        $catalogModel = $this->getModel('Catalog');
        $products     = count($productIds)
            ? $catalogModel->findAll('id IN (?)', array($productIds))
            : $catalogModel->createCollection();

        return array(
            'products'      => $products,
            'all_product'   => array_map(function($prod) use ($products) {
                                    $prod['obj'] = $products->getById($prod['id']);
                                    return $prod;
                                }, $this->getBasket()->getAll()),
            'all_count'     => $this->getBasket()->getCount(),
            'all_summa'     => $this->getBasket()->getSum(),
            'delivery'      => $delivery,
            'form'          => $form,
            'host'          => urlencode($this->config->get('siteurl').$this->router->createLink('basket') ),
            'auth'          => $this->auth,
        );
    }


    /**
     * Additional product to basket
     *
     * @param $_REQUEST['basket_prod_id']
     * @param $_REQUEST['basket_prod_name']
     * @param $_REQUEST['basket_prod_count']
     * @param $_REQUEST['basket_prod_price']
     * @param $_REQUEST['basket_prod_details']
     *
     * @return string
     */
    public function addAction()
    {
        $post = $this->request->request;

        $basket_prod_name = $post->get('basket_prod_name');
        $basket_prod_id   = $post->get('basket_prod_id');
        if (null !== $basket_prod_id || null !== $basket_prod_name) {
            $basket_prod_count      = $post->get('basket_prod_count');
            $basket_prod_price      = $post->get('basket_prod_price');
            $basket_prod_details    = $post->get('basket_prod_details');

            $this->getBasket()->add(
                $basket_prod_id,
                $basket_prod_name,
                $basket_prod_count,
                $basket_prod_price,
                $basket_prod_details
            );

            $this->getBasket()->save();
        }

        $this->getTpl()->assign(array(
            'count'     => $this->getBasket()->getCount(),
            'summa'     => $this->getBasket()->getSum(),
            'number'    => $this->getBasket()->count(),
            'path'      => $this->request->get('path'),
        ));

        return $this->renderJson(array(
            'count'  => $basket_prod_id ? $this->getBasket()->getCount($basket_prod_id) : null,
            'sum'    => number_format($this->getBasket()->getSum(), 2, ',', ''),
            'widget' => $this->getTpl()->fetch('basket.widget'),
            'msg'    => $basket_prod_name . '<br>'
                      . Sfcms::html()->link('добавлен в корзину',$this->router->createServiceLink('basket','index')),
        ));
    }

    /**
     * Deletion product from basket
     *
     * @return array
     * @throws NotFoundHttpException
     */
    public function deleteAction()
    {
        $key   = $this->request->get('key');
        $count = $this->request->get('count', 0);

        if (null === $key) {
            throw new \InvalidArgumentException('Parameter "key" not defined');
        }

        $item = $this->getBasket()->getByKey($key);
        if (!$item) {
            throw new NotFoundHttpException(sprintf('Item with key %d not found', $key));
        }
        $this->getBasket()->del($key, $count);
        $this->getBasket()->save();

        $this->app->getLogger()->info(sprintf('id: %d, count: %d', $key, $count));

        $this->getTpl()->assign(array(
                'count'     => $this->getBasket()->getCount(),
                'summa'     => $this->getBasket()->getSum(),
                'number'    => $this->getBasket()->count(),
                'path'      => $this->request->get('path'),
            ));

        return $this->renderJson(array(
            'count'  => $this->getBasket()->getCount(),
            'sum'    => number_format($this->getBasket()->getSum(), 2, ',', ''),
            'widget' => $this->getTpl()->fetch('basket.widget'),
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function countAction()
    {
        $id = $this->request->get('id');
        $count = $this->request->get('count');

        if (!$this->getBasket()->setCount($id, $count)) {
            return $this->renderJson(array(
                'id' => $id,
                'error' => 'This product not found',
            ));
        }

        $this->tpl->assign(array(
            'count'     => $this->getBasket()->getCount(),
            'summa'     => $this->getBasket()->getSum(),
            'number'    => $this->getBasket()->count(),
            'path'      => $this->request->get('path'),
        ));

        $this->getBasket()->save();

        $this->get('logger')->info('request', $this->request->request->all());
        $this->get('logger')->info('basket', $this->getBasket()->getAll());

        return $this->renderJson(array(
            'count'  => $this->getBasket()->getCount(),
            'sum'    => number_format($this->getBasket()->getSum(), 2, ',', ''),
            'widget' => $this->getTpl()->fetch('basket.widget'),
        ));
    }

    /**
     * Ajax validate
     * @param Form $form
     * @return array
     */
    private function formValidate(Form $form)
    {
        $result = array('error'=>0);

        if ($this->request->request->get('recalculate')) {
            // обновляем количества
            $basket_counts = $this->request->request->get('basket_counts');

            if ( $basket_counts && is_array( $basket_counts ) ) {
                /** @var $basket Sfcms\Basket\Base */
                array_walk($basket_counts, function($prod_count, $key, $basket){
                    $basket->setCount( $key, $prod_count > 0 ? $prod_count : 1 );
                }, $this->getBasket());
            }

            // Удалить запись
            $basket_del = $this->request->request->get('basket_del');
            if ( $basket_del && is_array( $basket_del ) ) {
                foreach( $basket_del as $key => $prod_del ) {
                    $this->getBasket()->del( $key );
                    $result['delete'][] = $key;
                }
            }

            $delivery = $this->app->getDelivery($this->request);
            $result['delivery']['cost'] = number_format( $delivery->cost(), 2, ',', '' );
            $result['basket'] = $this->getBasket()->getAll();
            $result['basket']['sum'] = $this->getBasket()->getSum() + $delivery->cost();
            $result['basket']['count'] = $this->getBasket()->getCount();
            $result['basket']['delitems'] = isset($result['delete']) ? $result['delete'] : array();
            unset( $result['delete'] );
            $this->getBasket()->save();
        }

        if ($this->request->request->get('do_order')) {
            if ($form->validate()) {
                // Создание заказа
                if ($this->getBasket()->getAll()) {
                    // создать заказ
                    $this->request->getSession()->set('delivery', $form['delivery_id']);
                    $delivery = $this->app->getDelivery($this->request);
//                    $this->request->getSession()->set('delivery',$delivery->getType());

                    /** @var $orderModel OrderModel */
                    $orderModel    = $this->getModel('Order');
                    $order = $orderModel->createOrder($form, $delivery);

                    if ($order) {
                        /** @var $orderPositionModel OrderPositionModel */
                        $orderPositionModel = $this->getModel('OrderPosition');

                        // Заполняем заказ товарами
                        $pos_list    = array();
                        foreach ($this->getBasket()->getAll() as $data) {
                            /** @var $position OrderPosition */
                            $position   = $orderPositionModel->createObject();
                            $position->attributes = array(
                                'ord_id'    => $order->getId(),
                                //'name'      => $data['name'],
                                'product_id'=> (int) $data['id'],
                                'articul'   => ! empty( $data['articul'] ) ? $data['articul'] : $data['name'],
                                'details'   => $data['details'],
                                'currency'  => isset( $data['currency'] ) ? $data['currency'] : $this->t('catalog','RUR'),
                                'item'      => isset( $data['item'] ) ? $data['item'] : $this->t('catalog', 'item'),
                                'cat_id'    => is_numeric( $data['id'] ) ? $data['id'] : '0',
                                'price'     => $data['price'],
                                'count'     => $data['count'],
                                'status'    => 1,
                            );
                            $position->save();

                            $pos_list[] = $position->attributes;
                        }

                        $event = new OrderEvent($order);
                        $this->app->getEventDispatcher()->dispatch('market.order.create', $event);

                        $this->tpl->assign(array(
                            'order'     => $order,
                            'sitename'  => $this->config->get('sitename'),
                            'ord_link'  => $this->config->get('siteurl').$order->getUrl(),
                            'user'      => $this->auth->getId() ? $this->auth->currentUser()->getAttributes() : array(),
                            'date'      => date('H:i d.m.Y'),
                            'order_n'   => $order->getId(),
                            'positions' => $pos_list,
                            'total_summa'=> $this->getBasket()->getSum() + $delivery->cost(),
                            'total_count'=> $this->getBasket()->getCount(),
                            'delivery'  => $delivery,
                            'sum'       => $this->getBasket()->getSum(),
                        ));

                        $this->sendmail(
                            $order->email,
                            $this->config->get('admin'),
                            sprintf('Новый заказ с сайта %s №%s',$this->config->get('sitename'),$order->getId()),
                            $this->tpl->fetch('order.mail.createadmin'),
                            'text/html'
                        );

                        $this->sendmail(
                            $this->config->get('admin'),
                            $order->email,
                            sprintf('Заказ №%s на сайте %s',$order->getId(),$this->config->get('sitename')),
                            $this->tpl->fetch('order.mail.create'),
                            'text/html'
                        );

                        $this->getBasket()->clear();
                        $this->getBasket()->save();

                        $this->request->getSession()->set('order_id',$order->id);
//                        $this->request->getSession()->getFlashBag()->add('info', 'Order is created successfully');

                        $paymentModel = $this->getModel('Payment');
                        $payment = $paymentModel->find( $form['payment_id'] );
                        $order->payment_id = $payment->getId();

                        $result['redirect'] = $order->getUrl();
                    }
                }
            } else {
                $result['error'] = 1;
                $result['errors'] = $form->getErrors();
            }
        }

        return $result;
    }

    /**
     * Fill Address from Yandex
     * @param string $address
     * @return array
     */
    private function fromYandexAddress( $address )
    {
        $yaAddress = new Sfcms\Yandex\Address();
        $yaAddress->setJsonData( $address );

        $return = array(
            'country'   => $yaAddress->country,
            'city'      => $yaAddress->city,
            'address'   => $yaAddress->getAddress(),
            'zip'       => $yaAddress->zip,
        );

        if ($yaAddress->firstname) {
            $return['fname'] = $yaAddress->firstname;
        }
        if ($yaAddress->lastname) {
            $return['lname'] = $yaAddress->lastname;
        }
        if ($yaAddress->email) {
            $return['email'] = $yaAddress->email;
        }
        if ($yaAddress->phone) {
            $return['phone'] = $yaAddress->phone;
        }
        if ($yaAddress->comment) {
            $return['comment'] = $yaAddress->comment;
        }

        return $return;

    }

}
