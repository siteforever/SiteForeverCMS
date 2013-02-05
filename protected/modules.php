<?php
/**
 * Controllers list
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

/**
 * Массив модулей
 *
 * Показывает, какие модули подключены к системе в данный момент
 *
 * - name: это имя модуля, под которым он будет находится в системе
 * - path: Это путь в пространстве имен, в котором находится модуль
 */
return array(
    array('name'=>'Banner',     'path'=>'Module\Banner'),
    array('name'=>'Catalog',    'path'=>'Module\Catalog'),
    array('name'=>'Feedback',   'path'=>'Module\Feedback'),
    array('name'=>'Gallery',    'path'=>'Module\Gallery'),
    array('name'=>'Guestbook',  'path'=>'Module\Guestbook'),
    array('name'=>'Market',     'path'=>'Module\Market'),
    array('name'=>'News',       'path'=>'Module\News'),
    array('name'=>'Page',       'path'=>'Module\Page'),
    array('name'=>'System',     'path'=>'Module\System'),
    array('name'=>'Foo',        'path'=>'Acme\Module\Foo'),
);
