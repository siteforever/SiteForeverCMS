<?php
/*
 * Smarty plugin
 *
 * -------------------------------------------------------------
 * File:     function.thumb.php
 * Type:     function
 * Name:     thumb
 * Purpose:  Выведет изображение предварительного просмотра
 * -------------------------------------------------------------
 *
 * $method: 1 - Add field, 2 - Crop
 *
 * @example {thumb src="/files/catalog/0001/trade.jpg" width="200"}
 *
 */
function smarty_function_thumb($params)
{
    //    var_dump($params);
    //    return "";
    return Sfcms::html()->thumb($params);
}
