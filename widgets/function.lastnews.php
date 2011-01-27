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
    switch( $params['sort'] ) {
        case 'rand':
            $sort   = 'RAND()';
            break;
        default:
            $sort   = 'news.date DESC';
    }

    if ( ! isset($params['limit']) ) {
        $params['limit']    = '3';
    }

    $where = array();
    $where[]    = "news.hidden = 0";
    $where[]    = "news.deleted = 0";

    if ( isset( $params['cat'] ) ) {
        $where[]    = " news.cat_id = :cat ";
    }

    $cat = '';

    /**
     * @var model_news $model
     */
    $model = Model::getModel('News');

    //$model->setCond( implode(' AND ', $where) );

    $list   = $model->findAllWithLinks(array(
        'cond'  => join(" AND ", $where),
        'params'=> array(':cat'=>$params['cat']),
        'order' => $sort,
        'limit' => $params['limit'],
    ));

    //$list   = $model->findAllWithLinks($params['limit']);

    if ( isset( $params['template'] ) ) {
        $tpl    = App::$tpl;
        $tpl->list  = $list;
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