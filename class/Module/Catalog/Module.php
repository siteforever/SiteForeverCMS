<?php
/**
 * Модуль каталога
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Catalog;

use Sfcms\Module as SfModule;
use Sfcms\Model;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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

    public static function relatedModel()
    {
        return 'Catalog';
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return array(
            'controllers' => array(
                'Goods'          => array(),
                'Prodtype'       => array(),
                'Catalog'        => array(),
                'Cataloggallery' => array( 'class' => 'Controller\Gallery', ),
                'CatalogComment' => array( 'class' => 'Controller\Comment', ),
            ),
            'models' => array(
                'Catalog'         => 'Module\Catalog\Model\CatalogModel',
                'CatalogGallery'  => 'Module\Catalog\Model\GalleryModel',
                'CatalogComment'  => 'Module\Catalog\Model\CommentModel',

                'ProductField'    => 'Module\Catalog\Model\FieldModel',
                'ProductProperty' => 'Module\Catalog\Model\PropertyModel',
                'ProductType'     => 'Module\Catalog\Model\TypeModel',
            ),
        );
    }

    public function registerRoutes(Router $router)
    {
        $routes = $router->getRouteCollection();
        $routes->add('catalog/delete',
            new Route('/catalog/delete',
                array('_controller'=>'catalog', '_action'=>'delete')
            ));
        $routes->add('catalog/save',
            new Route('/catalog/save',
                array('_controller'=>'catalog', '_action'=>'save')
            ));
        $routes->add('catalog/admin',
            new Route('/catalog/admin',
                array('_controller'=>'catalog', '_action'=>'admin')
            ));
        $routes->add('catalog/trade',
            new Route('/catalog/trade',
                array('_controller'=>'catalog', '_action'=>'trade')
            ));
        $routes->add('catalog/category',
            new Route('/catalog/category',
                array('_controller'=>'catalog', '_action'=>'category')
            ));
        $routes->add('catalog/move',
            new Route('/catalog/move',
                array('_controller'=>'catalog', '_action'=>'move')
            ));
        $routes->add('catalog/saveorder',
            new Route('/catalog/saveorder',
                array('_controller'=>'catalog', '_action'=>'saveorder')
            ));
        $routes->add('catalog/hidden',
            new Route('/catalog/hidden',
                array('_controller'=>'catalog', '_action'=>'hidden')
            ));

        $routes->add('catalogcomment/admin',
            new Route('/catalogcomment/admin',
                array('_controller'=>'catalogcomment', '_action'=>'admin')
            ));
        $routes->add('catalogcomment/edit',
            new Route('/catalogcomment/edit',
                array('_controller'=>'catalogcomment', '_action'=>'edit')
            ));
        $routes->add('catalogcomment/list',
            new Route('/catalogcomment/list',
                array('_controller'=>'catalogcomment', '_action'=>'list')
            ));

        $routes->add('cataloggallery/delete',
            new Route('/cataloggallery/delete',
                array('_controller'=>'cataloggallery', '_action'=>'delete')
            ));
        $routes->add('cataloggallery/markdefault',
            new Route('/cataloggallery/markdefault',
                array('_controller'=>'cataloggallery', '_action'=>'markdefault')
            ));
        $routes->add('cataloggallery/upload',
            new Route('/cataloggallery/upload',
                array('_controller'=>'cataloggallery', '_action'=>'upload')
            ));
        $routes->add('cataloggallery/watermark',
            new Route('/cataloggallery/watermark',
                array('_controller'=>'cataloggallery', '_action'=>'watermark')
            ));

        $routes->add('goods',
            new Route('/goods/search',
                array('_controller'=>'goods', '_action'=>'search')
            ));
        $routes->add('goods/admin',
            new Route('/goods/admin',
                array('_controller'=>'goods', '_action'=>'admin')
            ));
        $routes->add('goods/grid',
            new Route('/goods/grid',
                array('_controller'=>'goods', '_action'=>'grid')
            ));
        $routes->add('goods/edit',
            new Route('/goods/edit',
                array('_controller'=>'goods', '_action'=>'edit')
            ));
        $routes->add('goods/yml',
            new Route('/goods/yml',
                array('_controller'=>'goods', '_action'=>'yml')
            ));

        $routes->add('prodtype/admin',
            new Route('/prodtype/admin',
                array('_controller'=>'prodtype', '_action'=>'admin')
            ));
        $routes->add('prodtype/grid',
            new Route('/prodtype/grid',
                array('_controller'=>'prodtype', '_action'=>'grid')
            ));
        $routes->add('prodtype/edit',
            new Route('/prodtype/edit',
                array('_controller'=>'prodtype', '_action'=>'edit')
            ));
        $routes->add('prodtype/save',
            new Route('/prodtype/save',
                array('_controller'=>'prodtype', '_action'=>'save')
            ));
        $routes->add('prodtype/deletefield',
            new Route('/prodtype/deletefield',
                array('_controller'=>'prodtype', '_action'=>'deletefield')
            ));
    }


    public function init()
    {
        $model = Model::getModel('Catalog');
        $dispatcher = $this->app->getEventDispatcher();
        $dispatcher->addListener('plugin.page-catalog.save.start', array($model,'pluginPageSaveStart'));
        $dispatcher->addListener('plugin.page-catalog.resort', array($model,'pluginPageResort'));
    }

    public function registerService(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('catalog.product.form')) {
            $container->setDefinition('catalog.product.form', new Definition('Module\Catalog\Form\CatalogForm'));
        }
        $container->get('config')->setDefault('catalog', array(
            // сортировка товаров
            'order_list' => array(
                ''           => 'Без сортировки',
                'name'       => 'По наименованию',
                'price1'     => 'По цене (0->макс)',
                'price1-d'   => 'По цене (макс->0)',
                'articul'    => 'По артикулу',
            ),
            'order_default' => 'name',
            'onPage' => '10',
            'level'  => 0, // < 1 output all products
            'gallery_dir' => '/files/catalog/gallery',
                // 1 - добавление полей
                // 2 - обрезание лишнего

        ));
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Каталог',
                'sub'   => array(
                    array(
                        'name'  => $this->t('Goods'),
                        'url'   => 'goods/admin'
                    ),
                    array(
                        'name'  => $this->t('catalog','Product types'),
                        'url'   => 'prodtype/admin'
                    ),
                    array(
                        'name'  => $this->t('material','Materials'),
                        'url'   => 'material/admin'
                    ),
                    array(
                        'name'  => $this->t('Manufacturers'),
                        'url'   => 'manufacturers/admin'
                    ),
                    array(
                        'name'  => $this->t('Comments'),
                        'url'   => 'catalogcomment/admin',
                    ),
                    array(
                        'name'  => $this->t('Catalog'),
                        'url'   => 'catalog/admin',
                    ),
                )
            )
        );
    }

}
