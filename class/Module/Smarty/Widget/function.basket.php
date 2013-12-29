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
function smarty_function_basket($params, Smarty_Internal_Template $smarty)
{
    /** @var \Sfcms\Request $request */
    $request = $smarty->tpl_vars['request']->value;

    $basket = $request->getBasket();

    $smarty->smarty->assign(array(
        'count'     => $basket->getCount(),
        'summa'     => $basket->getSum(),
        'number'    => $basket->count(),
    ));
    return $smarty->smarty->fetch('basket/widget.tpl');
}
