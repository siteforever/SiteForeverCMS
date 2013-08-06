<?php

use Sfcms\Basket\Base as Basket;
use Module\User\Object\User;

/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class Sfcms_Basket_Factory
{
    static private $created = false;

    /**
     * Создаст корзину
     * @param \Sfcms\Request $request
     * @return Basket
     * @throws Exception
     */
    static function createBasket( \Sfcms\Request $request )
    {
        $auth = \App::cms()->getAuth();

//        if (!$user instanceof User) {
//            throw new Exception('Not valid User object');
//        }

        if ($auth->getPermission() != USER_GUEST) {
            $basket = new Sfcms_Basket_User($request, $auth->currentUser());
        } else {
            $basket = new Sfcms_Basket_Session($request, $auth->currentUser());
        }

        return $basket;
    }
}
