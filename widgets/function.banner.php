<?php
use Sfcms\Model;
use Module\Banner\Object\Banner;

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.banner.php
 * Type:     function
 * Name:     banner
 * Purpose:  Выдаст баннеры
 */
function smarty_function_banner($params)
{
    $modelBanner = Model::getModel('Banner');
    if (!isset($params['parent'])) {
        return 'You must specify a "parent" category id';
    }
    $criteria   = $modelBanner->createCriteria(array('order'=>'RAND()'));
    if (isset($params['parent']) && is_numeric($params['parent']) && $params['parent']) {
        $criteria->condition  = 'cat_id = ?';
        $criteria->params     = array($params['parent']);
    }

    /** @var $banner Banner */
    $banner = $modelBanner->find($criteria);
    if (!$banner) {
        return "";
    }
    $banner->count_show++;
//    $banner->save();

    App::cms()->getTpl()->assign(array(
        'banner'    => $banner,
    ));
    return App::cms()->getTpl()->fetch('banner.index');
}
