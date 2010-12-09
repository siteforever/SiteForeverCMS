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
    $url    = isset($params['url']) ? $params['url'] : 'catalog';
  
    $catalog = Model::getModel('model_Catalog');
    
    
    return $catalog->getMenu( $url, $parent, $level );
}
