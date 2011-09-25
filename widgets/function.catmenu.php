<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* File:     function.catmenu.php
* Type:     function
* Name:     catmenu
* Purpose:  Выведет меню каталога на сайте
* -------------------------------------------------------------
*/
function smarty_function_catmenu($params, $smarty)
{
    static $html = array();
    
    $parent = isset( $params['parent'] ) ? $params['parent'] : '0';
    $level  = isset( $params['level'] ) ? $params['level'] : '1';

    if ( isset($params['url']) ) {
        $url    = $params['url'];
    } else {
        $page_model = Model::getModel('Page');
        $page   = $page_model->find(array(
            'condition' => ' controller = ? AND link = ? ',
            'params'    => array( 'catalog', $parent ),
        ));
        if ( null !== $page ) {
            $url    = $page->alias;
        }
        else {
            $url    = 'index';
        }
    }

    $catalog = Model::getModel('Catalog');
    
    
    return $catalog->getMenu( $url, $parent, $level );
}
