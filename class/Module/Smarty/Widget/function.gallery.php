<?php
/**
 * Виджет галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
function smarty_function_gallery( $params, Smarty_Internal_Template $smarty )
{
    if ( empty( $params['tpl'] ) ) {
        $params['tpl'] = 'gallery/widget.tpl';
    }
    if ( empty( $params['limit'] ) ) {
        $params['limit'] = 4;
    }
    if ( empty( $params['gallery'] ) ) {
        return 'Required "gallery" param not defined';
    }

    $modelGallery = App::cms()->getModel('Gallery');
    /** @var $gallery Data_Object_Gallery */
    $images = $modelGallery->findAll('category_id = ? AND hidden = 0 AND deleted = 0',array($params['gallery']),'pos',$params['limit']);

    $smarty->assign('list', $images);
    return $smarty->fetch($params['tpl']);
}
