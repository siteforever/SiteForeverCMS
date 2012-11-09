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
use Form_Form;
use Forms_Basket_Address;
use Data_Object_Delivery;
use Model_Order;
use Model_Catalog;

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
        $this->request->setContent(t('basket','Basket'));
        $this->request->set('template', 'inner');

        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',t('Home'))
            ->addPiece(null,$this->request->getTitle());

        $deliveryModel = $this->getModel('Delivery');
        $deliveries = $deliveryModel->findAll('active = ?',array(1),'pos');
        $form->getField('delivery_id')->setVariants( $deliveries->column('name') );

        $paymentModel = $this->getModel('Payment');
        $payments = $paymentModel->findAll('active = ?', array(1));
        $form->getField('payment_id')->setVariants( $payments->column('name') );

        $delivery = null;
        if ( $delivId = filter_var( $_SESSION['delivery'], FILTER_SANITIZE_NUMBER_INT ) ) {
            $delivery = $deliveryModel->find( $delivId );
        }

        $productIds = array_map(function($b){
            return $b['id'];
        },$this->getBasket()->getAll());

        /** @var $catalogModel Model_Catalog */
        $catalogModel   = $this->getModel('Catalog');
        $products       = count($productIds)
            ? $catalogModel->findAll('id IN (?)', array($productIds))
            : $catalogModel->createCollection();

        $this->log( $productIds, 'basket' );

        return array(
            'products'      => $products,
            'all_product'   => $this->getBasket()->getAll(),
            'all_count'     => $this->getBasket()->getCount(),
            'all_summa'     => $this->getBasket()->getSum(),
            'delivery'      => $delivery,
            'form'          => $form,
            'host'          => urlencode($this->config->get('siteurl').$this->router->createLink('basket') ),
        );
    }


    /**
     * Добавит в корзину товар
     *
     * @param int $basket_prod_id
     * @param string $basket_prod_name
     *
     * @param $_REQUEST['basket_prod_count']
     * @param $_REQUEST['basket_prod_price']
     * @param $_REQUEST['basket_prod_details']
     *
     * @return string
     */
    public function addAction( $basket_prod_id, $basket_prod_name )
    {
        if ( $basket_prod_id || $basket_prod_name )
        {
            $basket_prod_count      = $this->request->get('basket_prod_count');
            $basket_prod_price      = $this->request->get('basket_prod_price');
            $basket_prod_details    = $this->request->get('basket_prod_details');

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
            'widget' => $this->getTpl()->fetch('basket.widget'),
            'msg'    => $basket_prod_name . ' '
                      . Sfcms::html()->link('добавлен в корзину',$this->router->createServiceLink('basket','index')),
        );

    }


    /**
     * Ajax validate
     * @param Form_Form $form
     * @return array
     */
    private function ajaxValidate( Form_Form $form )
    {
        $result = array('error'=>0);

        if ( $this->request->get('recalculate') ) {
            // обновляем количества
            $basket_counts = $this->request->get('basket_counts');
            if ( $basket_counts && is_array( $basket_counts ) ) {
                foreach( $basket_counts as $key => $prod_count ) {
                    //print "$key : $prod_count<br>";
                    $this->getBasket()->setCount( $key, $prod_count );
                }
            }

            // Удалить запись
            $basket_del = $this->request->get('basket_del');
            if ( $basket_del && is_array( $basket_del ) ) {
                foreach( $basket_del as $key => $prod_del ) {
                    $this->getBasket()->del( $key );
                    $result['delete'][] = $key;
                }
            }

            $delivery = null;
            if ( $deliveryId = filter_var( $_SESSION['delivery'], FILTER_SANITIZE_NUMBER_INT ) ) {
                /** @var $delivery Data_Object_Delivery */
                $delivery = $this->getModel('Delivery')->find($deliveryId);
            }
            $result['basket'] = $this->getBasket()->getAll();
            $result['basket']['sum'] = $this->getBasket()->getSum() + ($delivery ? $delivery->cost : 0);
            $result['basket']['count'] = $this->getBasket()->getCount();
            $result['basket']['delitems'] = $result['delete'];
            unset( $result['delete'] );
            $this->getBasket()->save();
        }

        if ( $this->request->get('do_order') ) {
            if ( $form->validate() ) {
                // Создание заказа
                if ( $this->getBasket()->getAll() ) {
                    // создать заказ

                    $delivery = $this->getModel('Delivery')->find( $form['delivery_id'] );
                    $_SESSION['delivery'] = $delivery->id;

                    /** @var $orderModel Model_Order */
                    $orderModel    = $this->getModel('Order');
                    $order = $orderModel->createOrder( $this->getBasket()->getAll(), $form, $delivery );

                    if ( $order ) {
                        $this->getBasket()->clear();
                        $this->getBasket()->save();

                        $_SESSION['order_id'] = $order->id;

                        $paymentModel = $this->getModel('Payment');
                        $payment = $paymentModel->find( $form['payment_id'] );
                        $order->payment_id = $payment->getId();

                        $result['redirect'] = $this->router->createServiceLink( 'order', 'create' );
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