<?php

use Sfcms\Model;
use Module\Page\Model\PageModel;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.menu.php
 * Type:     function
 * Name:     menu
 * Purpose:  Выведет меню на сайте
 * -------------------------------------------------------------
 */
function smarty_function_menu($params, Smarty_Internal_Template $smarty)
{
    /** @var \Sfcms\Request $request */
    $request = $smarty->tpl_vars['request']->value;
    /** @var $model PageModel */
    $model  = Model::getModel('Page');

    $resolver = new OptionsResolver();
    $resolver->setDefaults(array(
            'parent' => 0,
            'level'  => 0,
            'template' => 'menu',
            'class' => '',
            'currentId' => $request->get('id'),
            'parents'   => $model->getParents(),
        ));

    $smarty->assign($params = $resolver->resolve($params));

    return $smarty->fetch(sprintf('%s.tpl', $params['template']));
}
