<?php
/**
 * Создаст тэг ссылки
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

function smarty_block_a($params, $content)
{
    if (isset($params['href'])) {
        $href = $params['href'];
        unset($params['href']);
        unset($params['url']);
    } elseif (isset($params['url'])) {
        $href = $params['url'];
        unset($params['href']);
        unset($params['url']);
    } else {
        $href = '#';
    }

    $class = '';
    if (isset($params['class'])) {
        if (is_string($params['class'])) {
            $class = $params['class'];
        }
        if (is_array($params['class'])) {
            $class = implode(' ', $params['class']);
        }
        unset($params['class']);
    }

    if (null !== $content) {
        return Sfcms::html()->link($content, $href, $params, $class);
    }

    return '';
}
