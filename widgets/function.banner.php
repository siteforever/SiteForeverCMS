<?php
/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.banner.php
 * Type:     function
 * Name:     banner
 * Purpose:  Выдаст баннеры
 */
function smarty_function_banner( $params )
{
    $modelBanner = Model::GetModel('Banner');
    $countBanner = $modelBanner->count();

    if($countBanner==0)
    {
        print "Error! Banner not found!";
        return;
    }

    $criteria   = array('order'=>'RAND()');
    if ( isset( $params['parent'] ) && is_numeric( $params['parent'] ) && $params['parent'] ) {
        $criteria['condition']  = 'cat_id = ?';
        $criteria['params']     = array( $params['parent'] );
    }

    $banner = $modelBanner->find($criteria);
    $banner['count_show']=$banner['count_show']+1;
    $modelBanner->save( $banner );

    App::getInstance()->getTpl()->assign(array(
        'banner'    => $banner,
    ));
    return App::getInstance()->getTpl()->fetch('banner.index');
}