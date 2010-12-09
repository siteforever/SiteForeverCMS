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
    App::$tpl->assign(array(
        'count'     => App::$basket->getCount(),
        'summa'     => App::$basket->getSum(),
        'number'    => App::$basket->count(),
    ));
    return App::$tpl->fetch('basket.widget');
    //return App::$tpl->fetch('system:basket.widget');
}