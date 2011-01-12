<?php
/**
 * Класс корзиные пользователя
 * Сохраняет данные в профиль
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class UserBasket extends Basket
{

    function load()
    {
        $model  = Model::getModel('model_User');
        $this->data = $model->getBasketArray( $this->user );

        // Если были данные в сессии, то сохранить их пользователю
        if ( isset($_SESSION['basket']) && is_array($_SESSION['basket']) )
        {
            foreach ( $_SESSION['basket'] as $basket ) {
                //printVar($basket);
                $this->add( $basket['id'], $basket['count'], $basket['price'], $basket['details'] );
            }
            $this->save();
            //die(__CLASS__.'::'.__FUNCTION__.'('.__LINE__.')');
            unset( $_SESSION['basket'] );
        }
    }

    function save()
    {
        $model  = Model::getModel('model_User');
        $model->setBasketFromArray( $this->getAll(), $this->user );
        //$this->user->setBasketFromArray( $this->getAll() );
    }

}