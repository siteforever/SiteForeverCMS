<?php
/**
 * Контроллер корзины
 * @author KelTanas
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
namespace Module\Market\Controller;

use Sfcms;
use Sfcms_Controller;
use Sfcms\Form\Form;
use Forms_Basket_Address;
use Module\Market\Object\Delivery;
use Module\Market\Model\OrderModel;
use Module\Catalog\Model\CatalogModel;

class BasketController extends Sfcms_Controller
{
    public function indexAction( $address )
    {
        $form = new Forms_Basket_Address();

        // Ajax validate
        if ( $this->request->isAjax() && $form->getPost() ) {
            return $this->ajaxValidate( $form );
        }

        // Fill Address from current user
        if ( $this->user->hasPermission( USER_USER ) ) {
            $form->getField('fname')->setValue( $this->user->fname );
            $form->getField('lname')->setValue( $this->user->lname );
            $form->getField('email')->setValue( $this->user->email );
            $form->getField('phone')->setValue( $this->user->phone );
            $form->getField('address')->setValue( $this->user->address );
        }

        // Fill Address from Yandex
        if ( $address ) {
            $form->setData( $this->fromYandexAddress( $address ) );
        }

//        $catalogModel    = $this->getModel('Catalog');

        $this->request->setTitle(t('basket','Basket'));
        $this->request->set('template', 'inner');

        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',t('Home'))
            ->addPiece(null,$this->request->getTitle());

        // Заполним методы доставки
        $deliveryModel = $this->getModel('Delivery');
        $deliveries = $deliveryModel->findAll('active = ?',array(1),'pos');
        $form->getField('delivery_id')->setVariants( $deliveries->column('name') );

        $delivery = $this->app()->getDelivery();
        $form->getField('delivery_id')->setValue( $delivery->getType() );

        // Заполним методы оплаты
        $paymentModel = $this->getModel('Payment');
        $payments = $paymentModel->findAll('active = ?', array(1));
        $form->getField('payment_id')->setVariants( $payments->column('name') );
        $form->getField('payment_id')->setValue( $payments->rewind() ? $payments->rewind()->getId() : 0 );

        $metroModel = $this->getModel('Metro');
        $metro      = $metroModel->findAll('city_id = ?',array(2),'name');
        $form->getField('metro')->setVariants( $metro->column('name') );

        // Список ID продуктов
        $productIds = array_filter( array_map(function($b){
            return isset( $b['id'] ) ? $b['id'] : false;
        },$this->getBasket()->getAll()) );

        // Получаем товары из каталога
        /** @var $catalogModel CatalogModel */
        $catalogModel   = $this->getModel('Catalog');
        $products       = count($productIds)
            ? $catalogModel->findAll('id IN (?)', array($productIds))
            : $catalogModel->createCollection();

//        $this->log( $productIds, 'basket' );
//        $this->log( $this->getBasket()->getAll(), 'basket' );

        return array(
            'products'      => $products,
            'all_product'   => array_map(function( $prod ) use ($products) {
                                    return array_merge( $prod, array('obj'=>$products->getById($prod['id'])) );
                                }, $this->getBasket()->getAll()),
            'all_count'     => $this->getBasket()->getCount(),
            'all_summa'     => $this->getBasket()->getSum(),
            'delivery'      => $this->app()->getDelivery(),
            'form'          => $form,
            'host'          => urlencode($this->config->get('siteurl').$this->router->createLink('basket') ),
        );
    }


    /**
     * Добавит в корзину товар
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
        if ( $basket_prod_id || $basket_prod_name ) {
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

        return array(
            'id'     => $basket_prod_id,
            'count'  => $this->getBasket()->getCount( $basket_prod_id ),
            'widget' => $this->getTpl()->fetch('basket.widget'),
            'msg'    => $basket_prod_name . '<br>'
                      . Sfcms::html()->link('добавлен в корзину',$this->router->createServiceLink('basket','index')),
        );

    }


    /**
     * Ajax validate
     * @param Form $form
     * @return array
     */
    private function ajaxValidate( Form $form )
    {
        $result = array('error'=>0);

        if ( $this->request->get('recalculate') ) {
            // обновляем количества
            $basket_counts = $this->request->get('basket_counts');

            if ( $basket_counts && is_array( $basket_counts ) ) {
                /** @var $basket Sfcms\Basket\Base */
                array_walk($basket_counts, function($prod_count, $key, $basket){
                    $basket->setCount( $key, $prod_count > 0 ? $prod_count : 1 );
                }, $this->getBasket());
            }

            // Удалить запись
            $basket_del = $this->request->get('basket_del');
            if ( $basket_del && is_array( $basket_del ) ) {
                foreach( $basket_del as $key => $prod_del ) {
                    $this->getBasket()->del( $key );
                    $result['delete'][] = $key;
                }
            }

            $delivery = $this->app()->getDelivery();
            $result['delivery']['cost'] = number_format( $delivery->cost(), 2, ',', '' );
            $result['basket'] = $this->getBasket()->getAll();
            $result['basket']['sum'] = $this->getBasket()->getSum() + $delivery->cost();
            $result['basket']['count'] = $this->getBasket()->getCount();
            $result['basket']['delitems'] = isset($result['delete']) ? $result['delete'] : array();
            unset( $result['delete'] );
            $this->getBasket()->save();
        }

        if ( $this->request->get('do_order') ) {
            if ( $form->validate() ) {
                // Создание заказа
                if ( $this->getBasket()->getAll() ) {
                    // создать заказ

                    $delivery = $this->app()->getDelivery();
                    $this->app()->getSession()->set('delivery',$delivery->getType());

                    /** @var $orderModel OrderModel */
                    $orderModel    = $this->getModel('Order');
                    $order = $orderModel->createOrder( $this->getBasket(), $form, $delivery );

                    if ( $order ) {
                        $this->getBasket()->clear();
                        $this->getBasket()->save();

                        $this->app()->getSession()->set('order_id',$order->id);

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

        if ( $yaAddress->firstname )
            $return['fname'] = $yaAddress->firstname;
        if ( $yaAddress->lastname )
            $return['lname'] = $yaAddress->lastname;
        if ( $yaAddress->email )
            $return['email'] = $yaAddress->email;
        if ( $yaAddress->phone )
            $return['phone'] = $yaAddress->phone;
        if ( $yaAddress->comment )
            $return['comment'] = $yaAddress->comment;

        return $return;

    }

}