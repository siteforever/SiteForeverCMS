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
    if($countBanner==1){
        $banner  = $modelBanner->find(array());
    }
    else{
        $banners = $modelBanner->findAll();
        $banner  = $banners[rand(0,$countBanner-1)];
    }
    $banner['count_show']=$banner['count_show']+1;
    $modelBanner->save( $banner );

    App::getInstance()->getTpl()->assign(array(
        'banner'    => $banner,
    ));
    return App::getInstance()->getTpl()->fetch('banner.index');
}