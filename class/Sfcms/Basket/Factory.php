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
        if (\App::getInstance()->getAuth()) {
            $user = \App::getInstance()->getAuth()->currentUser();
        } else {
            $user = null;
        }


//        if (!$user instanceof User) {
//            throw new Exception('Not valid User object');
//        }

        if ($user && $user->getPermission() != USER_GUEST) {
            $basket = new Sfcms_Basket_User($request, $user);
        } else {
            $basket = new Sfcms_Basket_Session($request, $user);
        }

        return $basket;
    }
}
