<?php
/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class Basket_Factory
{
    static private $created = false;
    
    /**
     * Создаст корзину
     * @param $user
     * @return basket
     */
    static function createBasket( Data_Object_User $user )
    {
        if ( self::$created ) {
            throw new Exception('Корзина может быть создана только один раз');
        }
        self::$created = true;

        if ( $user->perm != USER_GUEST ) {
            $basket = new Basket_User( $user );
        }
        else {
            $basket = new Basket_Session( $user );
        }
        return $basket;
    }
}