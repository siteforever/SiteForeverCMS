<?php
/**
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
/*
* Smarty plugin
* -------------------------------------------------------------
* File:     function.lastnews.php
* Type:     function
* Name:     lastnews
* Purpose:  Выведет последние новости
* -------------------------------------------------------------
*/
function smarty_function_lastnews( $params, $smarty )
{
    if ( isset( $params['sort'] ) ) {
        switch( $params['sort'] ) {
            case 'rand':
                $sort   = 'RAND()';
                break;
        }
    }
    else {
        $sort   = '`date` DESC';
    }

    if ( ! isset($params['limit']) ) {
        $params['limit']    = '3';
    }

    $where = array();
    $where[]    = "hidden = 0";
    $where[]    = "deleted = 0";

    $param      = array();

    if ( isset( $params['cat'] ) ) {
        $where[]    = " cat_id = :cat ";
        $param[':cat']  = $params['cat'];
    }

    $cat = '';

    /**
     * @var model_news $model
     */
    $model = Model::getModel('News');

    //$model->setCond( implode(' AND ', $where) );

    $list   = $model->findAllWithLinks(array(
        'cond'  => join(" AND ", $where),
        'params'=> $param,
        'order' => $sort,
        'limit' => $params['limit'],
    ));

    foreach ( $list as $key => $item ) {
        //print $key;
        //printVar( $item->getAttributes() );
    }

    //printVar( $list );

    //$list   = $model->findAllWithLinks($params['limit']);

    if ( isset( $params['template'] ) ) {
        $tpl    = App::$tpl;
        $tpl->list  = $list;
        //return $params['template'];
        $content    = $tpl->fetch( $params['template'] );
    }
    else {
        $content     = array('<ul>');
        foreach ( $list as $l ) {
            $content[]  = "<li><a ".href($l['link'], array('doc'=>$l['id'])).">{$l['name']}</a></li>";
        }
        $content[]  = '</ul>';
        $content    = join('', $content);
    }

    return $content;
}