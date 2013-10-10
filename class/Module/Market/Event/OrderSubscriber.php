<?php
namespace Module\Market\Event;

use Module\Market\Form\OrderForm;
use Module\Market\Model\OrderModel;
use Module\Market\Model\OrderPositionModel;
use Sfcms\Config;
use Sfcms\Data\Collection;
use Sfcms\Model;
use Sfcms\Request;
use Sfcms\Yandex\Address;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author: keltanas <keltanas@gmail.com>
 */
class OrderSubscriber extends ContainerAware implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            'market.order.create' => array(
                array('fillOrderAddress', 100),
                array('fillOrderDelivery', 50),
                array('fillOrderPayment', 20),
                array('addOrderItems', 10),
                array('checkout', 0),
            ),
        );
    }

    /**
     * @return OrderForm
     */
    private function getForm()
    {
        return $this->container->get('order.form');
    }

    /**
     * @param OrderEvent $event
     */
    public function fillOrderAddress(OrderEvent $event)
    {
        $this->container->get('logger')->info('market.order.create.fillOrderAddress');
        $order = $event->getOrder();
        /** @var OrderForm $form */
        $form = $this->getForm();
        // Ajax validate
        if ($form->getPost($event->getRequest())) {
            if ($form->validate()) {
                $order->attributes = $form->getData();
//                $metro = $form->metro ? $this->getModel('Metro')->find($form->metro) : null;
                $order->address = join(', ',
                    array_filter(array(
                        '0' == $form->person ? 'Физическое лицо' : 'Юридическое лицо',
                        $form->zip,
                        $form->country,
                        $form->region,
                        $form->city,
//                        null === $metro ? false : $this->t('subway') . ' ' . $metro->name,
                        $form->address,
                        $form->details,
                        $form->passport,
                    ))
                );
                $order->status = 1;
                $order->paid = 0;
                $order->date = time();
                $order->user_id = $this->container->get('auth')->getId();
            }
        } else {
            // Fill Address from current user
            if ($this->container->get('auth')->hasPermission(USER_USER)) {
                $form->setData($this->container->get('auth')->currentUser()->attributes);
            }

            // Fill Address from Yandex
            $address = $event->getRequest()->get('address'); // yandex address
            if ($address) {
                $form->setData($this->fromYandexAddress($address));
            }
        }
    }

    /**
     * Fill Address from Yandex
     * @param string $address
     * @return array
     */
    private function fromYandexAddress($address)
    {
        $this->container->get('logger')->info('market.order.create.fromYandexAddress');
        $yaAddress = new Address();
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


    /**
     * @param OrderEvent $event
     */
    public function fillOrderDelivery(OrderEvent $event)
    {
        $this->container->get('logger')->info('market.order.create.fillOrderDelivery');
        if ($this->getForm()->isSent($event->getRequest())) {
            $event->getRequest()->getSession()->set('delivery', $this->getForm()->delivery_id);
            $event->getDeliveryManager()->setType($this->getForm()->delivery_id);
        } else {
            $this->getForm()->getField('delivery_id')->setValue(
                $event->getDeliveryManager()->getType()
            );
        }
        $event->getOrder()->delivery_id = $event->getDeliveryManager()->getType();
        $event->getOrder()->Delivery = $event->getDeliveryManager()->getObject();
    }

    /**
     * @param OrderEvent $event
     */
    public function fillOrderPayment(OrderEvent $event)
    {
        $this->container->get('logger')->info('market.order.create.fillOrderPayment');
        $event->getOrder()->payment_id = $this->getForm()->payment_id;
    }

    /**
     * @param OrderEvent $event
     */
    public function addOrderItems(OrderEvent $event)
    {
        $this->container->get('logger')->info('market.order.create.addOrderItems');
        /** @var $orderPositionModel OrderPositionModel */
        $orderPositionModel = Model::getModel('OrderPosition');

        if ($event->getBasket()->count()) {
            $order = $event->getOrder();
            $order->Positions = new Collection();

            $productIds = array_map(function($item){
                return $item['id'];
            }, $event->getBasket()->getAll());

            $catalogModel = Model::getModel('Catalog');
            $products = $catalogModel->findAll('id IN (?)', array($productIds));

            // Заполняем заказ товарами
            foreach ($event->getBasket()->getAll() as $data) {
                $order->Positions->add($orderPositionModel->createObject(array(
                    'ord_id'    => $order->getId(),
                    'product_id'=> (int) $data['id'],
                    'articul'   => $products->getById($data['id'])->name,
                    'details'   => $data['details'],
                    'currency'  => isset($data['currency']) ? $data['currency'] : 'RUR',
                    'item'      => isset($data['item']) ? $data['item'] : 'item',
                    'cat_id'    => is_numeric($data['id']) ? $data['id'] : '0',
                    'price'     => $data['price'],
                    'count'     => $data['count'],
                    'status'    => 1,
                )));
            }
        }
    }

    /**
     * @param OrderEvent $event
     */
    public function checkout(OrderEvent $event)
    {
        $this->container->get('logger')->info('market.order.create.checkout');
        if ($event->getRequest()->request->has('do_order')) {
            if ($this->getForm()->validate()) {

                $event->getOrder()->save();

                $event->getBasket()->clear();
                $event->getBasket()->save();
                $event->getRequest()->getSession()->set('order_id',$event->getOrder()->id);

                /** @var Config $config */
                $config = $this->container->get('config');
                $this->container->get('tpl')->assign(array(
                    'order'     => $event->getOrder(),
                    'basket'    => $event->getBasket(),
                    'delivery'  => $event->getDeliveryManager(),
                    'deliveryManager' => $event->getDeliveryManager(),
                    'payment'   => $event->getOrder()->Payment,
                    'robokassa' => $event->getOrder()->getRobokassa(
                                        $event->getOrder()->Payment,
                                        $event->getDeliveryManager(),
                                        $config
                                    ),
                ));

                $this->container->get('controller')->sendmail(
                    $config->get('email_for_order', $config->get('admin')),
                    $config->get('email_for_order', $config->get('admin')),
                    sprintf('Новый заказ с сайта %s №%s', $config->get('sitename'), $event->getOrder()->getId()),
                    $this->container->get('tpl')->fetch('order.mail.createadmin'),
                    'text/html'
                );

                $this->container->get('controller')->sendmail(
                    $config->get('email_for_order', $config->get('admin')),
                    $event->getOrder()->email,
                    sprintf('Заказ №%s на сайте %s', $event->getOrder()->getId(), $config->get('sitename')),
                    $this->container->get('tpl')->fetch('order.mail.create'),
                    'text/html'
                );

            }
        }
    }
}
