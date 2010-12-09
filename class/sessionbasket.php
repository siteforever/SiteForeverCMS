<?php
/**
 * Êëàññ êîğçèíû â ñåññèè
 * Ñîõğàíÿåò êîğçèíó â ñåññèş
 * @author Ermin Nikolay
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class SessionBasket extends Basket
{
    function load()
    {
        if ( isset($_SESSION['basket']) ) {
            $this->data = $_SESSION['basket'];
        }
        else {
            $this->data = array();
            $this->save();
        }
    }
    
    function save()
    {
        $_SESSION['basket'] = $this->data;
    }
}