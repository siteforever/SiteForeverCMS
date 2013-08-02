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

    public function init()
    {
        $model = Model::getModel('Catalog');
        $dispatcher = $this->app->getEventDispatcher();
        $dispatcher->addListener('plugin.page-catalog.save.start', array($model,'pluginPageSaveStart'));
        $dispatcher->addListener('plugin.page-catalog.resort', array($model,'pluginPageResort'));
    }

    public function registerService(ContainerBuilder $container)
    {
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
                        'name'  => 'Каталог',
                        'url'   => 'catalog/admin',
                    ),
                )
            )
        );
    }

}
