<?php
/**
 * Виджет выводит контент страницы с id=#, если он не защищен
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

/**
 * @param  $params['id']
 * @return string
 */
function smarty_function_page($params)
{
    if ( ! isset( $params['id'] ) || ! is_int( $params['id'] ) ) {
        return 'Using {page id=57}';
    }
    $model   = \App::cms()->getDataManager()->getModel('Page');
    $page   = $model->find( $params['id'] );

    if ( ! $page ) {
        return 'Page with id='.$params['id'].' not found';
    }

    if (!App::cms()->getAuth()->hasPermission($page->protected)) {
        return App::cms()->getContainer()->get('i18n')->write('Page content protected');
    }

    return $page->content;
}
