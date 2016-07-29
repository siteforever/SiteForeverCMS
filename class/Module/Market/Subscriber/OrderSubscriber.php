<?php
namespace Module\Market\Subscriber;

use Module\Market\Event\Event;
use Module\Market\Event\OrderEvent;
use Module\Market\Event\PaymentEvent;
use Module\Market\Form\OrderForm;
use Module\Market\Model\OrderPositionModel;
use Sfcms\Data\Collection;
use Sfcms\Data\DataManager;
use Sfcms\LoggerInterface;
use Sfcms\Model;
use Sfcms\Tpl\Driver;
use Sfcms\Yandex\Address;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Order processing
 * @author: keltanas <keltanas@gmail.com>
 */
class OrderSubscriber implements EventSubscriberInterface
{
    /** @var  OrderForm */
    private $form;

    /** @var  LoggerInterface */
    private $logger;

    /** @var Driver */
    private $tpl;

    /** @var string */
    private $email;

    /** @var string */
    private $sitename;

    /** @var EventDispatcher */
    private $dispatcher;

    /** @var DataManager */
    private $dataManager;

    public function __construct(OrderForm $form, DataManager $dataManager, LoggerInterface $logger, Driver $tpl, EventDispatcher $dispatcher, $email, $sitename)
    {
        $this->form = $form;
        $this->logger = $logger;
        $this->tpl = $tpl;
        $this->dispatcher = $dispatcher;
        $this->email = $email;
        $this->sitename = $sitename;
        $this->dataManager = $dataManager;
    }

    /**
     * @return \Module\Market\Form\OrderForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return \Sfcms\Tpl\Driver
     */
    public function getTpl()
    {
        return $this->tpl;
    }

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
     * @param OrderEvent $event
     */
    public function fillOrderAddress(OrderEvent $event)
    {
        $this->getLogger()->info('market.order.create.fillOrderAddress');
        $order = $event->getOrder();
        /** @var OrderForm $form */
        $form = $this->getForm();

        $auth = $event->getController()->auth;

        // Ajax validate
        if ($form->handleRequest($event->getRequest())) {
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
                $order->user_id = $auth->getId();
            }
        } else {
            // Fill Address from current user
            if ($auth->hasPermission(USER_USER)) {
                $form->setData($auth->currentUser()->attributes);
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
        $this->getLogger()->info('market.order.create.fromYandexAddress');
        $yaAddress = new Address();
        $yaAddress->setJsonData($address);

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
        $this->getLogger()->info('market.order.create.fillOrderDelivery');
        if ($this->getForm()->isSent($event->getRequest())) {
            $event->getRequest()->getSession()->set('delivery', $this->getForm()->delivery_id);
            $event->getDeliveryManager()->setType($this->getForm()->delivery_id);
        } else {
            $this->getForm()->getChild('delivery_id')->setValue(
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
        $this->getLogger()->info('market.order.create.fillOrderPayment');
        $event->getOrder()->payment_id = $this->getForm()->payment_id;
    }

    /**
     * @param OrderEvent $event
     */
    public function addOrderItems(OrderEvent $event)
    {
        $this->getLogger()->info('market.order.create.addOrderItems');
        /** @var $orderPositionModel OrderPositionModel */
        $orderPositionModel = $this->dataManager->getModel('OrderPosition');

        if ($event->getBasket()->count()) {
            $order = $event->getOrder();
            $order->Positions = new Collection();

            $productIds = array_map(function($item){
                return $item['id'];
            }, $event->getBasket()->getAll());

            $catalogModel = $this->dataManager->getModel('Catalog');
            $products = $catalogModel->findAll('id IN (?)', array($productIds));

            // Заполняем заказ товарами
            foreach ($event->getBasket()->getAll() as $data) {
                $product = $products->getById($data['id']);
                $order->Positions->add($orderPositionModel->createObject(array(
                    'ord_id'    => $order->getId(),
                    'product_id'=> (int) $data['id'],
                    'articul'   => $product ? $product->name : null,
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
        $this->getLogger()->info('market.order.create.checkout');
        if ($event->getRequest()->request->has('do_order')) {
            if ($this->getForm()->validate()) {

                $event->getOrder()->save();
                $event->getBasket()->clear();
                $event->getBasket()->save();
                $event->getRequest()->getSession()->set('order_id',$event->getOrder()->id);

                $paymentEvent = new PaymentEvent($event->getDeliveryManager(), $event->getOrder());
                $this->dispatcher->dispatch(Event::ORDER_PAYMENT, $paymentEvent);

                $this->getTpl()->assign(array(
                    'order'     => $event->getOrder(),
                    'basket'    => $event->getBasket(),
                    'delivery'  => $event->getDeliveryManager(),
                    'deliveryManager' => $event->getDeliveryManager(),
                    'payment'   => $paymentEvent->getPayment(),
//                    'robokassa' => $event->getOrder()->getRobokassa(
//                        $event->getOrder()->Payment,
//                        $event->getDeliveryManager()
//                        $config
//                    ),
                ));

                $event->getController()->sendmail(
                    $this->email,
                    $this->email,
                    sprintf('Новый заказ с сайта %s №%s', $this->sitename, $event->getOrder()->getId()),
                    $this->getTpl()->fetch('order.mail.createadmin'),
                    'text/html'
                );

                $event->getController()->sendmail(
                    $this->email,
                    $event->getOrder()->email,
                    sprintf('Заказ №%s на сайте %s', $event->getOrder()->getId(), $this->sitename),
                    $this->getTpl()->fetch('order.mail.create'),
                    'text/html'
                );

            }
        }
    }
}
