<?php
/**
 * Модуль каталога
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Catalog;

use Module\Catalog\DependencyInjection\CatalogExtension;
use Sfcms\Module as SfModule;
use Sfcms\Model;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
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

    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new CatalogExtension());
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public static function config()
    {
        return array(
            'controllers' => array(
                'Goods'          => array(),
                'ProdType'       => array(),
                'Catalog'        => array(),
                'CatalogGallery' => array( 'class' => 'Module\Catalog\Controller\GalleryController', ),
                'CatalogComment' => array( 'class' => 'Module\Catalog\Controller\CommentController', ),
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

    public function registerRoutes()
    {
        $routes = new RouteCollection();
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
                array('_controller'=>'catalogcomment', '_action'=>'edit'),
                array(), array(), '', array(),
                array('PUT', 'POST')
            ));
        $routes->add('catalogcomment/delete',
            new Route('/catalogcomment/list',
                array('_controller'=>'catalogcomment', '_action'=>'delete'),
                array(), array(), '', array(),
                array('DELETE')
            ));
        $routes->add('catalogcomment/list',
            new Route('/catalogcomment/list',
                array('_controller'=>'catalogcomment', '_action'=>'list'),
                array(), array(), '', array(),
                array('GET')
            ));


        $routes->add('cataloggallery/index',
            new Route('/cataloggallery/index',
                array('_controller'=>'cataloggallery', '_action'=>'index')
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
        return $routes;
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Catalogue',
                'glyph' => 'th-list',
                'sub'   => array(
                    array(
                        'name'  => 'Goods',
                        'url'   => 'goods/admin'
                    ),
                    array(
                        'name'  => 'Product types',
                        'url'   => 'prodtype/admin'
                    ),
                    array(
                        'name'  => 'Materials',
                        'url'   => 'material/admin'
                    ),
                    array(
                        'name'  => 'Manufacturers',
                        'url'   => 'manufacturers/admin'
                    ),
                    array(
                        'name'  => 'Comments',
                        'url'   => 'catalogcomment/admin',
                    ),
                    array(
                        'name'  => 'Catalogue',
                        'url'   => 'catalog/admin',
                    ),
                )
            )
        );
    }
}
