<?php
/**
 * Контроллер корзины
 * @author KelTanas
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
namespace Module\Market\Controller;

use Module\Market\Event\OrderEvent;
use Module\Market\Form\OrderForm;
use Module\Market\Model\OrderPositionModel;
use Module\Market\Object\Order;
use Module\Market\Object\OrderPosition;
use Sfcms;
use Sfcms\Controller;
use Sfcms\Form\Form;
use Module\Market\Object\Delivery;
use Module\Market\Model\OrderModel;
use Module\Catalog\Model\CatalogModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BasketController extends Controller
{
    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction()
    {
        $this->logger->info('request', $this->request->request->all());
        /** @var OrderForm $form */
        $form = $this->get('order.form');
        $result = array('error'=>0);

        $this->logger->info('basket', $this->getBasket()->getAll());
        if ($this->request->request->has('basket_counts')) {
            // обновляем количества
            $basket_counts = $this->request->request->get('basket_counts');
            $this->logger->info('basket_counts', $basket_counts);

            if ($basket_counts && is_array($basket_counts)) {
                /** @var $basket Sfcms\Basket\Base */
                array_walk($basket_counts, function($prodCount, $key, $basket) {
                    $basket->setCount($key, $prodCount > 0 ? $prodCount : 1);
                }, $this->getBasket());
            }
        }

        if ($this->request->request->has('basket_del')) {
            // Удалить запись
            $basket_del = $this->request->request->get('basket_del');
            if ($basket_del && is_array($basket_del)) {
                foreach ($basket_del as $key => $prod_del) {
                    $this->getBasket()->del($key);
                    $result['delete'][] = $key;
                }
            }
        }

        $this->getBasket()->save();

        /** @var Order $order */
        $order = $this->getModel('Order')->createObject();
        $event = new OrderEvent(
            $order,
            $this->request,
            $this,
            $this->getBasket(),
            $this->app->getDeliveryManager($this->request, $order)
        );
        $this->app->getEventDispatcher()->dispatch('market.order.create', $event);

        if ($this->request->request->has('recalculate')) {
            $result['delivery']['cost'] = number_format($event->getDeliveryManager()->cost(), 2, ',', '');
            $result['basket'] = $event->getBasket()->getAll();
            $result['basket']['sum'] = $event->getBasket()->getSum() + $event->getDeliveryManager()->cost();
            $result['basket']['count'] = $event->getBasket()->getCount();
            $result['basket']['delitems'] = isset($result['delete']) ? $result['delete'] : array();
            unset($result['delete']);
            if ($form->getErrors()) {
                $result['error'] = 1;
                $result['errors'] = $form->getErrors();
            }
            if ($this->request->isAjax()) {
                return $this->renderJson($result);
            }
        }

        if ($order->id) { // was create
            $result['redirect'] = $event->getOrder()->getUrl();
            if ($this->request->isAjax()) {
                return $this->renderJson($result);
            } else {
                return $this->redirect($result['redirect']);
            }
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

        $deliveryManager = $this->app->getDeliveryManager($this->request, $order);
        $form->getField('delivery_id')->setValue($deliveryManager->getType());

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
            'delivery'      => $deliveryManager,
            'form'          => $form,
            'host'          => $this->request->getSchemeAndHttpHost() . rawurlencode($this->router->createLink('basket')),
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

        $basket= $this->getBasket();

        $this->getTpl()->assign(array(
            'count'     => $this->getBasket()->getCount(),
            'summa'     => $this->getBasket()->getSum(),
            'number'    => $this->getBasket()->count(),
            'path'      => $this->request->get('path'),
        ));

        $this->logger->info('added to basket', array(
                'count'  => $basket_prod_id ? $this->getBasket()->getCount($basket_prod_id) : null,
                'sum'    => number_format($this->getBasket()->getSum(), 2, ',', ''),
                'widget' => $this->getTpl()->fetch('basket.widget'),
                'msg'    => $basket_prod_name . '<br>' . Sfcms::html()->link(
                        $this->t('basket', 'was added to basket'),
                        $this->router->createServiceLink('basket','index')
                    ),
            ));

        return $this->renderJson(array(
            'count'  => $basket_prod_id ? $this->getBasket()->getCount($basket_prod_id) : null,
            'sum'    => number_format($this->getBasket()->getSum(), 2, ',', ''),
            'widget' => $this->getTpl()->fetch('basket.widget'),
            'msg'    => $basket_prod_name . '<br>' . Sfcms::html()->link(
                            $this->t('basket', 'was added to basket'),
                            $this->router->createServiceLink('basket','index')
                        ),
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


}
