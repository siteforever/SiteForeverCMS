<?php
/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class basketFactory
{
    static private $created = false;
    
    /**
     * Создаст корзину
     * @param $user
     * @return basket
     */
    static function createBasket( model_User $user )
    {
        if ( self::$created ) {
            throw new Exception('Корзина может быть создана только один раз');
        }
        self::$created = true;

        $user_perm = $user->getPermission();
        if ( $user_perm != USER_GUEST ) {
            $basket = new UserBasket( $user );
        }
        else {
            $basket = new SessionBasket( $user );
        }
        return $basket;
    }
}