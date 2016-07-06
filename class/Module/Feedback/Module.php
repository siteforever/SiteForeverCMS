<?php
/**
 * Модуль обратной связи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Feedback;

use Sfcms\Module as SfModule;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

class Module extends SfModule
{
    /**
     * @inherit
     */
    public static function relatedField()
    {
        return 'id';
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public static function config()
    {
        return array(
            'controllers' => array(
                'feedback'  => array(),
            ),
            'models'      => array(
            ),
        );
    }

    public function registerRoutes()
    {
        $routes = new RouteCollection();
        $routes->add('feedback',
            new Route('/feedback',
                array('_controller'=>'feedback', '_action'=>'index')
            ));

        return $routes;
    }


}
