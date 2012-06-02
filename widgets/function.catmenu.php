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
    $parent = isset( $params['parent'] ) ? $params['parent'] : '0';
    $level  = isset( $params['level'] ) ? $params['level'] : '1';

    if ( isset($params['url']) ) {
        $url    = $params['url'];
    } else {
        $pageModel  = Sfcms_Model::getModel('Page');
        /** @var $page Data_Object_Page */
        $page       = $pageModel->find(array(
            'condition' => ' controller = ? AND link = ? ',
            'params'    => array( 'catalog', $parent ),
        ));
        if ( null !== $page ) {
            $url = $page->get('alias');
        } else {
            $url = 'index';
        }
    }
    /** @var $catalog Model_Catalog */
    $catalog = Sfcms_Model::getModel('Catalog');

    $result = $catalog->getMenu( $url, $parent, $level );
    return $result;
}
