<?php
/**
 * Модуль поиска
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Module\Search;

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
        return array(
            'controllers' => array(
                'search' => array('Module\\Search\\Controller\\SearchController'),
            ),
            'model' => array(
//                'Search' => 'Module\\Search\\Model\\SearchModel',
            ),
        );
    }

    public function init()
    {
    }
}