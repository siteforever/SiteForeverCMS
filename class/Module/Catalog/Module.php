<?php
/**
 * Модуль каталога
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Catalog;

use Sfcms\Module as SfModule;
use Sfcms\Model;

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
        return include_once __DIR__ . '/config.php';
    }

    public function init()
    {
        $model = Model::getModel('Catalog');
        $dispatcher = $this->app->getEventDispatcher();
        $dispatcher->addListener('plugin.page-catalog.save.start', array($model,'pluginPageSaveStart'));
        $dispatcher->addListener('plugin.page-catalog.resort', array($model,'pluginPageResort'));
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
                        'name'  => 'Каталог',
                        'url'   => 'catalog/admin',
                    ),
                )
            )
        );
    }

}
