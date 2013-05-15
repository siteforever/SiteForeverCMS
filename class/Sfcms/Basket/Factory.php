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
     * @param User $user
     * @return Basket
     * @throws Exception
     */
    static function createBasket( User $user )
    {
        if ( self::$created ) {
            throw new Exception('Корзина может быть создана только один раз');
        }
        self::$created = true;

        if ( ! $user instanceof User ) {
            throw new Exception('Not valid User object');
        }

        if ( $user && $user->getPermission() != USER_GUEST ) {
            $basket = new Sfcms_Basket_User( $user );
        }
        else {
            $basket = new Sfcms_Basket_Session( $user );
        }
        return $basket;
    }
}
