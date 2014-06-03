<?php
/**
 * Модуль производителей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Market;

use Module\Market\DependencyInjection\MarketExtension;
use Sfcms\Module as SfModule;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

class Module extends SfModule
{
    /**
     * Вернет поле, которое связывает страницу с ее модулем
     * @static
     * @return string
     */
    public static function relatedField()
    {
        return 'id';
    }

    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new MarketExtension());
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return array(
            'controllers' => array(
                'Basket'        => array(),
                'Delivery'      => array(),
                'Manufacturers' => array( 'class' => 'Module\Market\Controller\ManufacturerController'),
                'Material'      => array(),
                'Order'         => array(),
                'OrderAdmin'    => array(),
                'Orderpdf'      => array(),
                'Xmlprice'      => array(),
                'Payment'       => array(),
                'Producttype'   => array(),
                'Robokassa'     => array(),
            ),
            'models' => array(
                'Delivery'         => 'Module\\Market\\Model\\DeliveryModel',
                'Manufacturers'    => 'Module\\Market\\Model\\ManufacturerModel',
                'Material'         => 'Module\\Market\\Model\\MaterialModel',
                'Metro'            => 'Module\\Market\\Model\\MetroModel',
                'Order'            => 'Module\\Market\\Model\\OrderModel',
                'OrderPosition'    => 'Module\\Market\\Model\\OrderPositionModel',
                'OrderStatus'      => 'Module\\Market\\Model\\OrderStatusModel',
                'Payment'          => 'Module\\Market\\Model\\PaymentModel',
            ),
        );
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();

        $locator = new FileLocator(__DIR__);
        $loader = new YamlFileLoader($locator);
        $routes->addCollection($loader->load('routes.yml'));

        $routes->add('delivery/admin',
            new Route('/delivery/admin',
                array('_controller'=>'delivery', '_action'=>'admin')
            ));
        $routes->add('delivery/edit',
            new Route('/delivery/edit',
                array('_controller'=>'delivery', '_action'=>'edit')
            ));
        $routes->add('delivery/sortable',
            new Route('/delivery/sortable',
                array('_controller'=>'delivery', '_action'=>'sortable')
            ));
        $routes->add('delivery/select',
            new Route('/delivery/select',
                array('_controller'=>'delivery', '_action'=>'select')
            ));

        $routes->add('manufacturers',
            new Route('/manufacturers',
                array('_controller'=>'Manufacturers', '_action'=>'index')
            ));
        $routes->add('manufacturers/admin',
            new Route('/manufacturers/admin',
                array('_controller'=>'Manufacturers', '_action'=>'admin')
            ));
        $routes->add('manufacturers/edit',
            new Route('/manufacturers/edit',
                array('_controller'=>'Manufacturers', '_action'=>'edit')
            ));
        $routes->add('manufacturers/save',
            new Route('/manufacturers/save',
                array('_controller'=>'Manufacturers', '_action'=>'save')
            ));
        $routes->add('manufacturers/delete',
            new Route('/manufacturers/delete',
                array('_controller'=>'Manufacturers', '_action'=>'delete')
            ));

        $routes->add('material',
            new Route('/material',
                array('_controller'=>'material', '_action'=>'index')
            ));
        $routes->add('material/admin',
            new Route('/material/admin',
                array('_controller'=>'material', '_action'=>'admin')
            ));
        $routes->add('material/grid',
            new Route('/material/grid',
                array('_controller'=>'material', '_action'=>'grid')
            ));
        $routes->add('material/edit',
            new Route('/material/edit',
                array('_controller'=>'material', '_action'=>'edit')
            ));
        $routes->add('material/save',
            new Route('/material/save',
                array('_controller'=>'material', '_action'=>'save')
            ));

        $routes->add('payment',
            new Route('/payment/admin',
                array('_controller'=>'payment', '_action'=>'admin')
            ));
        $routes->add('payment/edit',
            new Route('/payment/edit',
                array('_controller'=>'payment', '_action'=>'edit')
            ));
        $routes->add('payment/delete',
            new Route('/payment/delete',
                array('_controller'=>'payment', '_action'=>'delete')
            ));

        $routes->add('robokassa',
            new Route('/robokassa',
                array('_controller'=>'robokassa', '_action'=>'index')
            ));
        $routes->add('robokassa/success',
            new Route('/robokassa/success',
                array('_controller'=>'robokassa', '_action'=>'success')
            ));
        $routes->add('robokassa/result',
            new Route('/robokassa/result',
                array('_controller'=>'robokassa', '_action'=>'result')
            ));
        $routes->add('robokassa/fail',
            new Route('/robokassa/fail',
                array('_controller'=>'robokassa', '_action'=>'fail')
            ));
    }


    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Интернет магазин',
                'gliph' => 'euro',
                'sub'   => array(
                    array(
                        'name'  => $this->t('Payment'),
                        'url'   => 'payment/admin'
                    ),
                    array(
                        'name'  => $this->t('delivery','Delivery'),
                        'url'   => 'delivery/admin'
                    ),
                    array(
                        'name'  => 'Заказы',
                        'url'   => 'order/admin',
                    ),
                ),
            ),
        );
    }
}
