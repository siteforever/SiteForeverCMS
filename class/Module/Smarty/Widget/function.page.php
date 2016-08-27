<?php
/**
 * Виджет выводит контент страницы с id=#, если он не защищен
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

/**
 * @param  $params ['id']
 * @return string
 */
function smarty_function_page($params)
{
    if (!isset($params['id']) && !isset($params['alias'])) {
        return 'Using {page id=57} or {page alias="index"}';
    }
    /** @var \Module\Page\Model\PageModel $model */
    $model = \App::cms()->getDataManager()->getModel('Page');
    $page = null;
    if (isset($params['id']) && $params['id']) {
        $page = $model->find($params['id']);
    } elseif (isset($params['alias']) && $params['alias']) {
        $page = $model->findByRoute($params['alias']);
    }

    if (!$page) {
        return 'Page not found';
    }

    if (!App::cms()->getAuth()->hasPermission($page->protected)) {
        return App::cms()->getContainer()->get('i18n')->write('Page content protected');
    }

    if (isset($params['notice']) && filter_var($params['notice'], FILTER_VALIDATE_BOOLEAN)) {
        return $page->notice;
    }

    return $page->content;
}
