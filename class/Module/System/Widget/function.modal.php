<?php
/**
 * Выведет HTML код для модального окна TwitterBootstrap
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

/**
 * @param  $params['id']
 * @return string
 */
function smarty_function_modal( $params, Smarty_Internal_Template $smarty )
{
    if ( ! isset( $params['id'] ) ) {
        return '<div class="alert alert-error"><strong>Modal:</strong> Property "id" not defined</div>';
    }

    $smarty->assign(array(
        'id'    => $params['id'],
        'title' => isset( $params['title'] ) ? $params['title'] : 'undefined',
        'body'  => isset( $params['body'] ) ? $params['body'] : 'undefined',
    ));

    return $smarty->fetch('modal.tpl');
}
