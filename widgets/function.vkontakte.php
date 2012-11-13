<?php
/**
 * Вконтакте
 * @param array $params
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 * @return string
 */
function smarty_function_vkontakte( $params )
{
    /**
     * type: { full, button, mini, vertical }
     * verb: { 0: 'Нравится', 1: 'Интересно' }
     * height: { 18, 20, 22, 24 }
     */

    $type   = isset( $params['type'] ) ? $params['type'] : 'full';
    $verb   = isset( $params['verb'] ) && $params['verb'] ? ', verb: 1' : '';
    $height = isset( $params['height'] ) ? ', height:' . $params['height'] : '';
    $api_id = App::getInstance()->getConfig()->get('social.vk.id');

//    App::getInstance()->getRequest()->addScript('http://userapi.com/js/api/openapi.js?47');
    $return = array(
        '',
    );

//    "<script type=\"text/javascript\" src=\"\"></script>"

    return
    "<script type=\"text/javascript\" src=\"//vk.com/js/api/openapi.js?63\"></script>"
    ."<script type=\"text/javascript\">"
    ."VK.init({apiId: {$api_id}, onlyWidgets: true});"
    ."</script>"
    ."<div id=\"vk_like\"></div>"
    ."<script type=\"text/javascript\">"
    ."VK.Widgets.Like(\"vk_like\", {type: \"{$type}\"{$verb}{$height}});"
    ."</script>";
}