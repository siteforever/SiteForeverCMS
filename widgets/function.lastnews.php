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
    $sort   = '`date` DESC';

    if ( isset( $params['sort'] ) ) {
        switch( $params['sort'] ) {
            case 'rand':
                $sort   = 'RAND()';
                break;
        }
    }

    if ( ! isset($params['limit']) ) {
        $params['limit']    = '3';
    }

    $where = array();
    $where[]    = "hidden = 0";
    $where[]    = "deleted = 0";
//    $where[]    = "main = 0";

    $param      = array();

    if ( isset( $params['cat'] ) ) {
        $where[]    = " cat_id = :cat ";
        $param[':cat']  = $params['cat'];
    }

    /**
     * @var model_news $model
     */
    $model = Sfcms_Model::getModel('News');

    $list = $model->findAll( join( " AND ", $where ), $param, $sort, $params[ 'limit' ] );

    $tpl    = App::getInstance()->getTpl();

    if ( isset( $params['template'] ) ) {
        $tpl->assign('list', $list);
        //return $params['template'];
        $content    = $tpl->fetch( $params['template'] );
    } else {
        $content     = array('<ul>');
        foreach ( $list as $l ) {
            $content[]  = "<li>".Sfcms::html()->link($l->name, $l->url)."</li>";
        }
        $content[]  = '</ul>';
        $content    = join('', $content);
    }

    return $content;
}