<?php
/**
 * Виджет выводит контент страницы с id=#, если он не защищен
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

/**
 * @param  $params['id']
 * @return void
 */
function smarty_function_page( $params )
{
    if ( ! isset( $params['id'] ) || ! is_int( $params['id'] ) ) {
        return 'Using {page id="57"}';
    }
    $model   = Model::getModel('Structure');
    $page   = $model->find( $params['id'] );

    if ( ! $page ) {
        return 'Page with id='.$params['id'].' not found';
    }

    if ( App::getInstance()->getUser()->hasPermission( $page->protected )  ) {
        return 'Page content protected';
    }

    return $page->content;    
}