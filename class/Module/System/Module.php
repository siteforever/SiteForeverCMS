<?php
/**
 * Модуль системы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\System;

use Sfcms\Model;
use Sfcms\Module as SfModule;

class Module extends SfModule
{
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
        $model = Model::getModel('Module\\System\\Model\\LogModel');
        $dispatcher = $this->app->getEventDispatcher();
        $dispatcher->addListener('save.start', array($model,'pluginAllSaveStart'));
    }

    public function admin_menu()
    {
        return array(
            array(
                'name'  => 'Пользователи',
                'url'   => 'users/admin',
            ),
            array(
                'name'  => 'Журнал',
                'url'   => 'log/admin',
            ),
            array(
                'name'=> 'Сервис',
                'sub' => array(
                    array(
                        'name'  => 'Менеджер файлов',
                        'url'   => 'elfinder/finder',
                        'class' => 'filemanager',
                    ),
                    //            array(
                    //                'name'  => 'Архивация базы',
                    //                'url'   => '/_runtime/sxd',
                    //                'class' => 'dumper',
                    //            ),
                    array(
                        'name'  => 'Поиск',
                        'url'   => 'search/admin',
                    ),
                )
            ),
            //    array(
            //        'name' => 'Система',
            //        'sub' => array(
            //            array(
            //                'name'  => 'Маршруты',
            //                'url'   => 'routes/admin',
            //            ),
            //            array(
            //                'name'  => 'Конфигурация системы',
            //                'url'   => 'system',
            //            ),
            //            array(
            //                'name'  => 'Настройка',
            //                'url'   => 'setting/admin',
            //            ),
            //            array(
            //                'name'  => 'Генератор',
            //                'url'   => 'generator',
            //            ),
            //        ),
            //    ),
            array(
                'name'  => 'Выход',
                'url'   => 'users/logout',
            ),
        );
    }
}
