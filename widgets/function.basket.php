<?php
/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
/*
* Smarty plugin
* -------------------------------------------------------------
* File:     function.basket.php
* Type:     function
* Name:     basket
* Purpose:  Выведет данные о корзине
* -------------------------------------------------------------
*/
function smarty_function_basket()
{
    $basket = App::getInstance()->getBasket();

    App::getInstance()->getTpl()->assign(array(
        'count'     => $basket->getCount(),
        'summa'     => $basket->getSum(),
        'number'    => $basket->count(),
    ));
    return App::getInstance()->getTpl()->fetch('basket.widget');
    //return App::$tpl->fetch('system:basket.widget');
}