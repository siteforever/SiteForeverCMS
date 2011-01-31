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
        $this->data = array();
        if ( $this->user->basket )
        {
            $this->data = unserialize( $this->user->basket );
        }

        // Если были данные в сессии, то сохранить их пользователю
        if ( isset($_SESSION['basket']) && is_array($_SESSION['basket']) )
        {
            foreach ( $_SESSION['basket'] as $basket ) {
                $this->add( $basket['id'], $basket['count'], $basket['price'], $basket['details'] );
            }
            $this->save();
            unset( $_SESSION['basket'] );
        }
    }

    function save()
    {
        $this->user->basket   = serialize( $this->getAll() );

        //$model  = Model::getModel('User');
        //$model->setBasketFromArray( $this->getAll(), $this->user );
        //$this->user->setBasketFromArray( $this->getAll() );
    }

}