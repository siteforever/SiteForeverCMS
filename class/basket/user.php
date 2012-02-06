<?php
/**
 * Класс корзиные пользователя
 * Сохраняет данные в профиль
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class Basket_User extends Basket
{

    function load()
    {
        if ( $this->user->basket )
        {
            if ( ! $this->data = @unserialize( $this->user->basket ) ) {
                $this->data = array();
            }
        } else {
            $this->data = array();
        }

        // Если были данные в сессии, то сохранить их пользователю
        if ( isset($_SESSION['basket']) && is_array($_SESSION['basket']) )
        {
            foreach ( $_SESSION['basket'] as $basket ) {
                $this->add( $basket['id'], $basket['name'], $basket['count'], $basket['price'], $basket['details'] );
            }
            unset( $_SESSION['basket'] );
        }
        $this->save();
    }

    function save()
    {
        $this->user->basket   = serialize( $this->data );
        $this->user->getModel()->save( $this->user );
    }
}