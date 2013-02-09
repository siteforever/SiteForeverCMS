<?php
/**
 * Модуль Галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Gallery;

use Sfcms\Model;
use Sfcms\Module as SfModule;

class Module extends SfModule
{
    public static function relatedField()
    {
        return 'id';
    }

    public static function relatedModel()
    {
        return 'GalleryCategory';
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
        $model = Model::getModel('GalleryCategory');
        $dispatcher = $this->app->getEventDispatcher();
        $dispatcher->addListener('plugin.page-gallery.save.start', array($model,'pluginPageSaveStart'));
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Галерея',
                'url'   => 'gallery/admin',
            )
        );
    }
}
